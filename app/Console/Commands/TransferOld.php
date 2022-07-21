<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Models\Product;
use App\Models\Property;
use App\Models\PropertyOption;
use App\Models\PropertyToProduct;
use App\Models\Section;
use App\Models\Vendor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransferOld extends Command
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
    protected $signature = 'transfer:renew';

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
        $this->info('transfer sections');
        $this->transferSections();
        $this->info('end transfer sections');
        fprintf ( STDOUT, "%s", "\x07" );


        $this->info('transfer properties');
        $this->transferProperties();
        $this->info('end transfer properties');
        fprintf ( STDOUT, "%s", "\x07" );


        $this->info('transfer options');
        $this->transferOptions();
        $this->info('end transfer options');
        fprintf ( STDOUT, "%s", "\x07" );


        $this->info('transfer products');
        $this->transferProducts();
        $this->info('end transfer products');
        fprintf ( STDOUT, "%s", "\x07" );


        $this->info('transfer vendors');
        $this->transferVendors();
        $this->info('end transfer vendors');
        fprintf ( STDOUT, "%s", "\x07" );


        $this->info('transfer properties to product');
        $this->transferPropertyToProducts();
        $this->info('end transfer properties to product');
        fprintf ( STDOUT, "%s", "\x07" );


        $this->info('transfer product to sections');
        $this->transferProductToSections();
        $this->info('end transfer product to sections');
        fprintf ( STDOUT, "%s", "\x07" );


        return 0;
    }

    private function transferSections()
    {
        $sections = DB::table('old_base.sections')->get();

        foreach ($sections as $section) {
            $dbSection = Section::updateOrCreate([
                'code' => $section->code,
                'extId' => $section->id,
            ], [
                'title' => $section->title,
                'active' => true,
                'showMain' => (bool)$section->popular,
            ]);

            if ($section->image) {
                $dbImage = Image::updateOrCreate([
                    'path' => $section->image
                ], [
                    'title' => '',
                ]);

                $dbSection->imageId = $dbImage->id;
                $dbSection->save();
            }
        }

        $sectionables = DB::table('old_base.sectionables')->where('sectionables_type', '=', 'App\\Models\\Section')->get();

        foreach ($sectionables as $sectionable) {
            $parentSection = Section::where('extId', '=', $sectionable->section_id)->first();
            $subSection = Section::where('extId', '=', $sectionable->sectionables_id)->orderBy('id', 'desc')->first();

            if ($parentSection && $subSection) {
                $subSection->sectionId = $parentSection->id;
                $subSection->save();
            }
        }
    }

    private function transferProperties()
    {
        $properties = DB::table('old_base.properties')->get();

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

            if ($property->type === 'list') {
                $multiple = true;
            }

            $dbProperty = Property::updateOrCreate([
                'title' => $property->title,
                'extId' => $property->id,
                'code' => $property->code,
            ], [
                'valueType' => $valueType,
                'multiple' => $multiple,
            ]);
        }
    }

    private function transferOptions()
    {
        $options = DB::table('old_base.property_to_products')->whereNotNull('value')->get();

        foreach ($options as $option) {
            $value = null;

            $value = json_decode($option->value, true);
            if ($value['value']) {
                $dbProperty = null;

                if (is_float($value['value'])) {
                    $dbProperty = Property::where('extId', '=', $option->property_id)->first();

                    if ($dbProperty) {
                        $dbProperty->valueType = 'float';
                        $dbProperty->save();
                        $dbProperty->refresh();

                        PropertyOption::updateOrCreate([
                            'floatVal' => (float)$value['value'],
                            'propertyId' => $dbProperty->id,
                        ], [

                        ]);
                    }
                } elseif (is_numeric($value['value'])) {
                    $dbProperty = Property::where('extId', '=', $option->property_id)->first();

                    if ($dbProperty) {
                        if ($dbProperty->valueType !== 'float') {
                            $dbProperty->valueType = 'number';
                            $dbProperty->save();
                            $dbProperty->refresh();
                        }

                        $value = $dbProperty->valueType === 'number' ? (int)$value['value'] : (float)$value['value'];

                        PropertyOption::updateOrCreate([
                            'numberVal' => $value,
                            'propertyId' => $dbProperty->id,
                        ], [

                        ]);
                    }
                } elseif (is_string($value['value'])) {
                    $dbProperty = Property::where('extId', '=', $option->property_id)->first();

                    if ($dbProperty) {
                        if ($dbProperty->valueType === 'text' && strlen($value['value']) > 255) {
                            $dbProperty->valueType = 'text';
                            $dbProperty->save();
                            $dbProperty->refresh();
                        }

                        $type = $dbProperty->valueType === 'string' ? 'stringVal' : 'textVal';

                        PropertyOption::updateOrCreate([
                            "$type" => (string)$value['value'],
                            'propertyId' => $dbProperty->id,
                        ], [

                        ]);
                    }
                }
            }
        }
    }

    private function transferProducts()
    {
        $products = DB::table('old_base.products')->get();

        foreach ($products as $product) {
            $dbProduct = Product::updateOrCreate([
                'extId' => $product->ext_id,
                'code' => $product->code,
            ], [
                'title' => $product->title,
                'description' => $product->description,
                'active' => $product->active,
                'showMain' => $product->popular,
                'vendorCode' => $product->vendor_code,
                'showPrice' => $product->show_price,
                'price' => $product->price,
                'priceEur' => $product->priceE ?? null,
                'priceUsd' => $product->priceU ?? null,
                'updateEur' => $product->updateE ?? null,
                'updateUsd' => $product->updateU ?? null,
            ]);

            if ($product->image) {
                $dbImage = Image::updateOrCreate([
                    'path' => $product->image,
                ], [
                    'title' => null,
                ]);

                if ($dbImage) {
                    $dbProduct->imageId = $dbImage->id;
                    $dbProduct->save();
                }
            }
        }
    }

    private function transferVendors()
    {
        $vendors = DB::table('old_base.vendors')->get();

        foreach ($vendors as $vendor) {
            $dbVendor = Vendor::updateOrCreate([
                'code' => $vendor->code,
                'title' => $vendor->title,
            ], [

            ]);

            if ($vendor->image) {
                $dbImage = Image::updateOrCreate([
                    'path' => $vendor->image
                ], [
                    'title' => null,
                ]);

                if ($dbImage) {
                    $dbVendor->imageId = $dbImage->id;
                    $dbVendor->save();
                }
            }
        }
    }

    private function transferPropertyToProducts()
    {
        $options = DB::table('old_base.property_to_products')->whereNotNull('value')->get();

        foreach ($options as $option) {
            $value = json_decode($option->value, true);
            if ($value['value']) {
                if (is_float($value['value'])) {
                    $dbProperty = Property::where('extId', '=', $option->property_id)->first();

                    if ($dbProperty) {
                        $dbProperty->valueType = 'float';
                        $dbProperty->save();
                        $dbProperty->refresh();

                        $dbOption = PropertyOption::updateOrCreate([
                            'floatVal' => (float)$value['value'],
                            'propertyId' => $dbProperty->id,
                        ], [

                        ]);

                        $dbProduct = Product::where('extId', '=', $option->product_id)->first();

                        if ($dbProduct && $dbOption) {
                            PropertyToProduct::updateOrCreate([
                                'productId' => $dbProduct->id,
                                'propertyId' => $dbProperty->id,
                                'optionId' => $dbOption->id,
                            ], [

                            ]);
                        }
                    }
                } elseif (is_numeric($value['value'])) {
                    $dbProperty = Property::where('extId', '=', $option->property_id)->first();

                    if ($dbProperty) {
                        if ($dbProperty->valueType !== 'float') {
                            $dbProperty->valueType = 'number';
                            $dbProperty->save();
                            $dbProperty->refresh();
                        }

                        $value = $dbProperty->valueType === 'number' ? (int)$value['value'] : (float)$value['value'];

                        $dbOption = PropertyOption::updateOrCreate([
                            'numberVal' => $value,
                            'propertyId' => $dbProperty->id,
                        ], [

                        ]);

                        $dbProduct = Product::where('extId', '=', $option->product_id)->first();

                        if ($dbProduct && $dbOption) {
                            PropertyToProduct::updateOrCreate([
                                'productId' => $dbProduct->id,
                                'propertyId' => $dbProperty->id,
                                'optionId' => $dbOption->id,
                            ], [

                            ]);
                        }
                    }
                } elseif (is_string($value['value'])) {
                    $dbProperty = Property::where('extId', '=', $option->property_id)->first();

                    if ($dbProperty) {
                        if ($dbProperty->valueType === 'text' && strlen($value['value']) > 255) {
                            $dbProperty->valueType = 'text';
                            $dbProperty->save();
                            $dbProperty->refresh();
                        }

                        $type = $dbProperty->valueType === 'string' ? 'stringVal' : 'textVal';

                        $dbOption = PropertyOption::updateOrCreate([
                            "$type" => (string)$value['value'],
                            'propertyId' => $dbProperty->id,
                        ], [

                        ]);

                        $dbProduct = Product::where('extId', '=', $option->product_id)->first();

                        if ($dbProduct && $dbOption) {
                            PropertyToProduct::updateOrCreate([
                                'productId' => $dbProduct->id,
                                'propertyId' => $dbProperty->id,
                                'optionId' => $dbOption->id,
                            ], [

                            ]);
                        }
                    }
                }
            }
        }
    }

    private function transferProductToSections()
    {
        $dbProducts = Product::get();
        $dbRootSectionIds = Section::whereNull('sectionId')
            ->get()
            ->pluck('extId');

        foreach ($dbProducts as $dbProduct) {
            $section = DB::table('old_base.sectionables')
                ->where('sectionables_id', '=', $dbProduct->extId)
                ->whereNotIn('section_id', $dbRootSectionIds)
                ->first();

            if ($section) {
                $dbSection = Section::where('id', '=', $section->section_id)->first();

                if ($dbSection) {
                    $dbProduct->sectionId = $dbSection->id;
                    $dbProduct->save();
                }
            }
        }
    }

//    private function transfer
}
