<?php

namespace App\Console\Commands\Laravel;

use App\Models\Image;
use App\Models\Product;
use App\Models\Property;
use App\Models\PropertyOption;
use App\Models\PropertyToProduct;
use App\Models\Section;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferCatalog extends Command
{
    private array $skipProperties = [
        'специальное предложение',
        'цена',
        'артикул',
        'производитель',
        'ссылка',
        'новинки',
        'запросить стоимость',
        'деталировка',
        'цена (евро)',
        'цена (руб)',
        'цена (доллар)',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:catalog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
          $this->info('prepare sections');
        $this->prepareSections();

        $this->info('sync sections');
        $this->syncSections();

//        $this->info('sections');
//        $this->transferSections();
//
//        $this->info('sub sections');
//        $this->transferSubSections();

        $this->info('products');
        $this->transferProducts();

        $this->info('products to sections');
        $this->transferProductsToSections();

        $this->info('properties');
        $this->transferProperties();

        $this->info('fix images');
        $this->fixImages();

        $this->info('options');
        $this->transferOptions();

        $this->info('fix options');
        $this->fixOptions();

        $this->info('properties to products');
        $this->transferPropertyToProducts();

        return 0;
    }

    private function transferSections()
    {
        $arrSections = DB::table('old_base_backup.sections')
            ->get();

        $this->output->progressStart($arrSections->count());

        foreach ($arrSections as $section) {
            $dbSection = Section::updateOrCreate([
                'title' => $section->title,
                'extId' => $section->id,
                'bitrixExtId' => $section->ext_id,
            ], [
                'code' => Str::slug($section->title) . "_{$section->id}",
            ]);

            if ($section->image) {
                $dbImage = Image::updateOrCreate([
                    'path' => $section->image,
                ], [
                    'title' => '',
                ]);

                $dbSection->imageId = $dbImage->id;
                $dbSection->save();
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferSubSections()
    {
        $arrSections = DB::table('old_base_backup.sectionables')
            ->where('sectionables_type', '=', Section::class)
            ->get()->groupBy('sectionables_id');


        $this->output->progressStart($arrSections->count());

        foreach ($arrSections as $sectionId => $relation) {
            $section = Section::where('extId', '=', $sectionId)->first();
            $parent = Section::where('extId', '=', $relation[0]->section_id)->first();

            if ($section && $parent && $section->id !== $parent->id) {
                $section->sectionId = $parent->id;
                $section->save();
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferProducts()
    {
        $products = DB::table('old_base_backup.products')
            ->get();

        $this->output->progressStart($products->count());

        foreach ($products as $product) {
            $dbProduct = Product::updateOrCreate([
                'title' => $product->title,
                'code' => Str::slug($product->title . '_' . $product->vendorCode),
                'extId' => $product->id,
                'bitrixExtId' => $product->extId,
                'vendorCode' => $product->vendorCode,
            ], [
                'description' => $product->description,
                'price' => $product->price,
                'showPrice' => $product->showPrice,
                'priceEur' => $product->priceEur,
                'priceUsd' => $product->priceUsd,
                'updateEur' => $product->updateEur,
                'updateUsd' => $product->updateUsd,
                'showMain' => $product->showMain,
            ]);

            if ($product->imageId) {
                $image = DB::table('old_base_backup.images')
                    ->where('id', '=', $product->imageId)
                    ->first();

                $dbImage = Image::updateOrCreate([
                    'path' => $image->path,
                ], [
                    'title' => '',
                ]);

                $dbProduct->imageId = $dbImage->id;
                $dbProduct->save();
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferProductsToSections()
    {
        $pts = DB::table('old_base_backup.product_to_sections')
            ->get();

        $this->output->progressStart($pts->count());

        foreach ($pts as $relation) {
            $product = DB::table('old_base_backup.products')
                ->where('id', '=', $relation->productId)
                ->first();
            $section = DB::table('old_base_backup.sections')
                ->where('id', '=', $relation->sectionId)
                ->first();

            $dbProduct = Product::where('title', '=', $product->title)->first();
            $dbSection = Section::where('title', '=', $section->title)->first();

            if ($dbSection) {

                $counter = 0;

                while ($dbSection->sectionId) {
                    $dbSection = Section::find($section->sectionId);

                    $counter++;

                    if ($counter > 5) {
                        print_r($dbSection->sectionId . PHP_EOL);
                        break;
                    }
                }

                $dbProduct->sectionId = $dbSection->id;
                $dbProduct->save();
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferProperties()
    {
        $properties = DB::table('old_base.properties')->get();

        $this->output->progressStart($properties->count());

        foreach ($properties as $property) {
            $skip = false;

            foreach ($this->skipProperties as $skipProperty) {
                $pTitle = mb_strtolower($property->title);
                $sTitle = strtolower($skipProperty);

                if ($pTitle == $sTitle) {
                    $skip = true;
                    break;
                }
            }

            if ($skip) {
                continue;
            }

            $valueType = 'string';

            switch ($property->type) {
                case 'text':
                    $valueType = 'text';
                    break;
                case 'number':
                    $valueType = 'number';
                    break;
            }

            $multiple = false;

            $sections = Section::whereNull('sectionId')->get();

            foreach ($sections as $section) {
                Property::updateOrCreate([
                    'title' => $property->title,
                    'extId' => $section->id . '_' . $property->id,
                    'code' => Str::slug($property->title . '_' . $section->id),
                    'sectionId' => $section->id,
                ], [
                    'valueType' => $valueType,
                ]);
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferOptions()
    {
        $options = DB::table('old_base.property_to_products')->whereNotNull('value')->get();

        $this->output->progressStart($options->count());

        foreach ($options as $option) {
            $value = null;

            $value = json_decode($option->real_value, true);
            if ($value) {
                $dbProperties = Property::where('extId', 'like', "%\_{$option->property_id}")->get();

                if ($dbProperties) {
                    if ($dbProperties->count() > 0) {
                        foreach ($dbProperties as $dbProperty) {

                            if (is_float($value)) {
                                if ($dbProperty->valueType == 'string' || $dbProperty->valueType == 'text') {
                                    continue;
                                }

                                $dbProperty->valueType = 'float';
                                $dbProperty->save();
                                $dbProperty->refresh();

                                PropertyOption::updateOrCreate([
                                    'floatVal' => (float)$value,
                                    'propertyId' => $dbProperty->id,
                                    'sectionId' => $dbProperty->sectionId,
                                ], [

                                ]);
                            } elseif (is_numeric($value)) {
                                if ($dbProperty->valueType == 'string' || $dbProperty->valueType == 'text') {
                                    continue;
                                }
                                if ($dbProperty->valueType !== 'float') {
                                    $dbProperty->valueType = 'number';
                                    $dbProperty->save();
                                    $dbProperty->refresh();
                                }

                                $value = $dbProperty->valueType === 'number' ? (int)$value : (float)$value;

                                PropertyOption::updateOrCreate([
                                    'numberVal' => $value,
                                    'propertyId' => $dbProperty->id,
                                    'sectionId' => $dbProperty->sectionId,
                                ], [

                                ]);
                            } elseif (is_string($value)) {
                                if ($dbProperty->valueType === 'text' && strlen($value) > 255) {
                                    $dbProperty->valueType = 'text';
                                    $dbProperty->save();
                                    $dbProperty->refresh();
                                }

                                $type = $dbProperty->valueType === 'string' ? 'stringVal' : 'textVal';

                                PropertyOption::updateOrCreate([
                                    "$type" => (string)$value,
                                    'propertyId' => $dbProperty->id,
                                    'sectionId' => $dbProperty->sectionId,
                                ], [

                                ]);
                            }
                        }
                    }
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferPropertyToProducts()
    {
        $options = DB::table('old_base.property_to_products')->whereNotNull('value')->get();

        $this->output->progressStart($options->count());

        foreach ($options as $option) {
            $value = $option->real_value;
            try {
                if ($value) {
                    $dbProduct = Product::where('extId', '=', $option->product_id)->first();
                    $dbSection = null;

                    if ($dbProduct->sectionId) {
                        $dbSection = Section::where('id', '=', $dbProduct->sectionId)->first();

                        if ($dbSection->sectionId) {
                            $dbSection = null;
                            continue;
                        }
                    }

                    if ($dbSection) {
                        $dbProperty = Property::where('extId', '=', "{$dbSection->id}_{$option->property_id}")->first();

                        if ($dbProduct && $dbProperty) {
                            $dbOption = null;
                            switch ($dbProperty->valueType) {
                                case 'string':
                                    $dbOption = PropertyOption::where('stringVal', '=', (string)$value)
                                        ->where('propertyId', '=', $dbProperty->id)
                                        ->first();
                                    break;
                                case 'text':
                                    $dbOption = PropertyOption::where('textVal', '=', (string)$value)
                                        ->where('propertyId', '=', $dbProperty->id)
                                        ->first();
                                    break;
                                case 'float':
                                    $dbOption = PropertyOption::where('floatVal', '=', (float)$value)
                                        ->where('propertyId', '=', $dbProperty->id)
                                        ->first();
                                    break;
                                case 'number':
                                    $dbOption = PropertyOption::where('numberVal', '=', (float)$value)
                                        ->where('propertyId', '=', $dbProperty->id)
                                        ->first();
                                    break;
                                default:
                                    $dbOption = null;
                            }

                            if ($dbOption) {
                                PropertyToProduct::updateOrCreate([
                                    'productId' => $dbProduct->id,
                                    'propertyId' => $dbProperty->id,
                                    'sectionId' => $dbProperty->sectionId,
                                ], [
                                    'optionId' => $dbOption->id,
                                ]);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {

            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function fixImages()
    {
        $images = Image::whereNot('path', 'like', '/upload%')->get();

        $this->output->progressStart($images->count());

        foreach ($images as $image) {
            $image->path = '/uploads' . $image->path;
            $image->save();

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function fixOptions()
    {
        $properties = Property::where('valueType', '=', 'float')
            ->get();

        foreach ($properties as $property) {
            $options = PropertyOption::where('propertyId', '=', $property->id)
                ->whereNotNull('numberVal')
                ->get();

            foreach ($options as $option) {
                $option->floatVal = (float)$option->numberVal;
                $option->numberVal = null;
                $option->save();
            }
        }

        $properties = Property::where('valueType', '=', 'text')
            ->get();

        foreach ($properties as $property) {
            $options = PropertyOption::where('propertyId', '=', $property->id)
                ->whereNotNull('stringVal')
                ->get();

            foreach ($options as $option) {
                $option->textVal = (string)$option->stringVal;
                $option->stringVal = null;
                $option->save();
            }
        }
    }

    private function prepareSections()
    {
        $sectionsJson = file_get_contents(storage_path('ren_structure.json'));
        $sectionsArr = json_decode($sectionsJson, true);

        foreach ($sectionsArr as $rootSection) {
            $dbRootSection = Section::updateOrCreate([
                'title' => $rootSection['title'],
                'code' => !empty($rootSection['code']) ? $rootSection['code'] : Str::slug($rootSection['title']),
            ], [

            ]);

            if (!empty($rootSection['sections'])) {
                foreach ($rootSection['sections'] as $subSection) {
                    $this->subSections($dbRootSection, $subSection);
                }
            }
        }
    }

    private function subSections(Section $root, $section)
    {
        $dbRootSection = Section::updateOrCreate([
            'title' => $section['title'],
            'code' => !empty($section['code']) ? $section['code']: Str::slug($section['title']),
        ], [
            'sectionId' => $root->id,
        ]);

        if (!empty($section['sections'])) {
            foreach ($section['sections'] as $subSection) {
                $this->subSections($dbRootSection, $subSection);
            }
        }
    }

    private function syncSections()
    {
        $sections = DB::table('old_base_new.sections')
            ->get();

        $this->output->progressStart($sections->count());

        foreach ($sections as $section) {
            $dbSection = Section::where('title', '=', $section->title)
                ->first();

            if (!$dbSection) {
                $this->error($section->title . ':::' . $section->id);
                continue;
            }

            $imageId = null;

            if ($section->imageId) {
                $image = DB::table('old_base_new.images')
                    ->where('id', '=', $section->imageId)
                    ->first();

                if ($image) {
                    $dbImage = Image::updateOrCreate([
                        'path' => $image->path,
                    ], [
                        'title' => '',
                    ]);

                    $imageId = $dbImage->id;
                }
            }

            $dbSection->bitrixExtId = $section->bitrixExtId;
            $dbSection->extId = $section->extId;
            $dbSection->imageId = $imageId;
            $dbSection->save();

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
