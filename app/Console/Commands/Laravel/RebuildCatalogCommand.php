<?php

namespace App\Console\Commands\Laravel;

use App\Models\Section;
use Illuminate\Console\Command;

class RebuildCatalogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:rebuild {type?}';

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
        switch ($this->argument('type')) {
            case 'input':
                $this->buildFromJson();
                break;
            case 'output':
                $this->writeJson();
                break;
            default: 
                $this->error('Type not found. Enter corect type');
                break;
        }



        return 0;
    }

    private function writeJson()
    {
        $output = [];

        $rootSections = Section::whereNull('sectionId')->get();

        foreach ($rootSections as $rootSection) {
            $outputNode = [
                'title' => $rootSection->title,
                'sections' => [],
            ];

            if ($rootSection->sections) {
                foreach ($rootSection->sections as $subSection) {
                    $outputNode['sections'][] = [
                        'id' => $subSection->id,
                        'title' => $subSection->title,
                        'code' => $subSection->code,
                    ];
                }
            }

            $output[] = $outputNode;
        }

        file_put_contents(storage_path('structure.json'), json_encode($output));
    }

    private function buildFromJson()
    {
        $sections = Section::get();

        foreach ($sections as $section) {
            $section->title = str_replace(['\t', '  '], ' ', $section->title);
            $section->title = trim($section->title);
            $section->save();
        }
    }
}
