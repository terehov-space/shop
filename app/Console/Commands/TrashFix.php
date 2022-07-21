<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductToSection;
use Illuminate\Console\Command;

class TrashFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:trash';

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
        $trashed = Product::withoutGlobalScopes()->whereNotNull('deleted_at')->get();

        foreach ($trashed as $product) {
            if ($product->deleted_at) {
                ProductToSection::where('productId', $product->id)->delete();
            } else {
                die('deb');
            }
        }

        return 0;
    }
}
