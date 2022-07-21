<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductToSection;
use Illuminate\Console\Command;

class FixTitle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:title';

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
        $products = Product::where('vendorCode', 'like', '%quot%')->get();

        foreach ($products as $product) {
            $product->vendorCode = htmlspecialchars_decode($product->vendorCode);
            $product->save();
        }

        return 0;
    }
}
