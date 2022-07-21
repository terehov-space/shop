<?php

namespace App\Console\Commands\Laravel;

use App\Models\Product;
use App\Models\ProductToSection;
use App\Models\Section;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransferBitrixSub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:relations';

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
        $this->info('fix sections bitrixExtId');
        $this->fixSectionsBitrix();

        $this->info('fix bitrixExtId');
        $this->fixBitrixExtIds();

        $this->info('transfer from b_iblock_element_section');
        $this->transferProductToSectionBitrix();

        $this->info('transfer from b_iblock_element');
        $this->transferProductBitrix();

        $this->info('fix main section');
        $this->fixMainSection();

        return 0;
    }

    private function transferProductToSectionBitrix()
    {
        $products = DB::table('old_base_old.b_iblock_element')
            ->select('ID AS bitrixExtId')
            ->where('IBLOCK_ID', '=', 2)
            ->where('ACTIVE', '=', 'Y')
            ->get();

        $this->output->progressStart($products->count());

        foreach ($products as $product) {
            $dbProduct = Product::where('bitrixExtId', '=', $product->bitrixExtId)->first();

            if ($dbProduct) {

                $subSections = DB::table('old_base_old.b_iblock_section_element')
                    ->where('IBLOCK_ELEMENT_ID', '=', $product->bitrixExtId)
                    ->get();

                if ($subSections) {
                    foreach ($subSections as $subSection) {
                        $dbSection = Section::where('bitrixExtId', '=', $subSection->IBLOCK_SECTION_ID)
                            ->whereNotNull('sectionId')
                            ->first();

                        if ($dbSection) {
                            ProductToSection::updateOrCreate([
                                'sectionId' => $dbSection->id,
                                'productId' => $dbProduct->id,
                            ], []);
                        }
                    }
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferProductBitrix()
    {
        $products = DB::table('old_base_old.b_iblock_element')
            ->select('ID AS bitrixExtId')
            ->where('IBLOCK_ID', '=', 2)
            ->where('ACTIVE', '=', 'Y')
            ->get();

        $this->output->progressStart($products->count());

        foreach ($products as $product) {
            $dbProduct = Product::where('bitrixExtId', '=', $product->bitrixExtId)->first();

            if ($dbProduct) {
                $dbSection = Section::where('bitrixExtId', '=', $dbProduct->bitrixExtId)
                    ->whereNotNull('sectionId')
                    ->first();

                if ($dbSection) {
                    ProductToSection::updateOrCreate([
                        'sectionId' => $dbSection->id,
                        'productId' => $dbProduct->id,
                    ], []);
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function fixBitrixExtIds()
    {
        $totalFixed = 0;

        $vendorCodes = DB::table('old_base_old.b_iblock_element_property')
            ->where('IBLOCK_PROPERTY_ID', '=', 6)
            ->whereNotNull('VALUE')
            ->get();

        $this->output->progressStart($vendorCodes->count());

        foreach ($vendorCodes as $vendorCode) {
            $code = trim($vendorCode->VALUE);

            $dbProduct = Product::where('vendorCode', 'like', "%$code%")
                ->first();

            if ($dbProduct) {
                try {
                    $dbProduct->bitrixExtId = $vendorCode->IBLOCK_ELEMENT_ID;
                    $dbProduct->save();

                    $totalFixed++;
                } catch (\Throwable $e) {

                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->error("total fixed: {$totalFixed}");
    }

    private function fixMainSection()
    {
        $dbProducts = Product::get();

        $this->output->progressStart($dbProducts->count());

        foreach ($dbProducts as $dbProduct) {
            $dbSubSection = ProductToSection::where('productId', '=', $dbProduct->id)->first();

            if ($dbSubSection) {
                $subSection = Section::where('id', '=', $dbSubSection->sectionId)->first();

                $dbProduct->sectionId = $subSection->sectionId;
                $dbProduct->save();
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function fixSectionsBitrix()
    {
        $sections = DB::table('old_base_old.b_iblock_section')
            ->where('IBLOCK_ID', '=', 2)
            ->get();

        $this->output->progressStart($sections->count());

        foreach ($sections as $section) {
            $dbSections = Section::where('title', '=', $section->NAME)
                ->get();

            foreach ($dbSections as $dbSection) {
                if ($dbSection->title == $section->NAME) {
                    try {
                        $dbSection->bitrixExtId = $section->ID;
                        $dbSection->save();
                    } catch (\Throwable $e) {

                    }
                }
            }


            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
