<?php

namespace App\Console\Commands;

use App\Models\Property;
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
    protected $signature = 'transfer:catalog';

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


        $this->info('transfer properties');
        $this->transferSubSections();
        $this->info('end transfer properties');

        return 0;
    }

    private function transferSections()
    {
//        $jsonSections = file_get_contents(storage_path('app/restore.json'));
//        $arrSections = json_decode($jsonSections, true);
        $sectionToSection = DB::table('old_base_old.sectionables')
            ->where('sectionables_type', '=', Section::class)
            ->get()->pluck('section_id');

        $arrSections = DB::table('old_base_old.sections')
            ->whereIn('id', $sectionToSection)
            ->get();

        $this->output->progressStart($arrSections->count());

        foreach ($arrSections as $section) {
            $dbSection = Section::updateOrCreate([
                'title' => $section->title,
                'extId' => $section->id,
                'code' => Str::slug($section->title) . "_{$section->id}",
            ], []);

            if (!empty($section->sections)) {
                foreach ($section->sections as $subsection) {
                    $this->createSection($subsection, $dbSection);
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferSubSections()
    {
        $sectionToSection = DB::table('old_base_old.sectionables')
            ->where('sectionables_type', '=', Section::class)
            ->get()->pluck('sectionables_id');

        $arrSections = DB::table('old_base_old.sections')
            ->whereIn('id', $sectionToSection)
            ->get();

        $this->output->progressStart($arrSections->count());

        foreach ($arrSections as $section) {
            $relations = DB::table('old_base_old.sectionables')
                ->distinct()
                ->where('sectionables_type', '=', Section::class)
                ->where('sectionables_id', '=', $section->id)
                ->get();

            if ($relations->count() > 1) {
                die('test');
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function createSection($section, $parent)
    {
        $dbSection = Section::updateOrCreate([
            'title' => $section->title,
            'extId' => $section->ext_id,
            'code' => Str::slug("{$parent->title}_{$section->title}"),
            'sectionId' => $parent->id,
        ], []);

        if (!empty($section['sections'])) {
            foreach ($section['sections'] as $subsection) {
                $this->createSection($subsection, $dbSection);
            }
        }
    }

    private function transferProperties()
    {
        $properties = DB::table('old_base.properties')
            ->get();

        $sections = Section::whereNull('sectionId')->get();

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

            foreach ($sections as $section) {
                Property::updateOrCreate([
                    'title' => $property->title,
                    'sectionId' => $section->id,
                    'code' => Str::slug("{$property->code}_{$section->id}"),
                ], [
                    'valueType' => $valueType,
                ]);
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
