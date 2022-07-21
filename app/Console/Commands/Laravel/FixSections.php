<?php

namespace App\Console\Commands\Laravel;

use App\Models\Product;
use App\Models\ProductToSection;
use App\Models\Section;
use Illuminate\Console\Command;

class FixSections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:fixSections';

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
        $pts = ProductToSection::get();

        foreach ($pts as $pt) {
            $product = Product::find($pt->productId);
            $section = Section::find($pt->sectionId);

            $rootSection = Section::findRootSection($section);
            if ($rootSection->id !== $product->sectionId) {
                $pt->delete();
            }
        }

        return 0;
    }
}
