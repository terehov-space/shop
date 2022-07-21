<?php

namespace App\Console\Commands;

use App\Models\Section;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferSections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:sections';

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
        $this->transferSections();
        $this->transferSubSections();

        return 0;
    }

    private function transferSections()
    {
        $sections = DB::table('old_base_old.b_iblock_section')
            ->where('IBLOCK_ID', '=', 2)
            ->where('ACTIVE', '=', 'Y')
            ->whereNull('IBLOCK_SECTION_ID')
            ->get();

        $this->output->progressStart($sections->count());

        foreach ($sections as $section) {
            Section::updateOrCreate([
                'extId' => $section->ID,
                'code' => Str::slug($section->NAME),
            ], [
                'title' => $section->NAME,
            ]);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferSubSections()
    {
        $sections = DB::table('old_base_old.b_iblock_section')
            ->where('IBLOCK_ID', '=', 2)
            ->where('ACTIVE', '=', 'Y')
            ->whereNotNull('IBLOCK_SECTION_ID')
            ->get();

        $this->output->progressStart($sections->count());

        foreach ($sections as $section) {
            $parentSection = Section::where('extId', '=', $section->IBLOCK_SECTION_ID)->first();

            if ($parentSection) {
                Section::updateOrCreate([
                    'extId' => $section->ID,
                    'code' => Str::slug($parentSection->title) . '_' . Str::slug($section->NAME),
                ], [
                    'title' => $section->NAME,
                    'sectionId' => $parentSection->id,
                ]);
            }

            $this->output->progressAdvance();
        }
    }
}
