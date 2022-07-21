<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Section;
use App\Models\Product;
use App\Models\ProductToSection;

class Fix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sss:fff';

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
        $codes = [
            'monoblok-vysokogo-davleniia-hrc-1416-et-27111-annovi-reverberi-27111',
            'monoblok-vysokogo-davleniia-hrr-1520-et28087-27112-annovi-reverberi-28087',
            'monoblok-vysokogo-davleniia-hrr-1520-et-2808627113-annovi-reverberi-28086',
        ];

        $products = Product::whereIn('code', $codes)->get();

        foreach ($products as $product) {
            ProductToSection::where('sectionId', '=', 74)
                ->where('productId', '=', $product->id)
                ->delete();

            ProductToSection::updateOrCreate([
                'productId' => $product->id,
                'sectionId' => 67
            ], []);
        }

        return 0;
    }
}
