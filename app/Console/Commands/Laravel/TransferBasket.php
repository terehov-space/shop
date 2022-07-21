<?php

namespace App\Console\Commands\Laravel;

use App\Models\Basket;
use App\Models\Product;
use App\Models\ProductToBasket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransferBasket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:basket';

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
        $this->info('baskets');
        $this->transferBaskets();

        $this->info('product to baskets');
        $this->transferOrders();

        $this->info('recount');
        $this->recountBasket();

        return 0;
    }

    private function transferBaskets()
    {
        $baskets = DB::table('old_base.baskets')
            ->whereNotNull('email')
            ->get();

        $this->output->progressStart($baskets->count());

        foreach ($baskets as $basket) {
            Basket::updateOrCreate([
                'code' => $basket->code,
            ], [
                'email' => $basket->email,
                'phone' => $basket->phone,
            ]);
        }

        $this->output->progressFinish();
    }

    private function transferOrders()
    {
        $baskets = DB::table('old_base.baskets')
            ->whereNotNull('email')
            ->get();

        $this->output->progressStart($baskets->count());

        foreach ($baskets as $basket) {
            $products = DB::table('old_base.product_to_baskets')
                ->where('basket_id', '=', $basket->id)
                ->get();

            $dbBasket = Basket::where('code', '=', $basket->code)->first();

            foreach ($products as $product) {
                $dbProduct = Product::where('extId', '=', $product->product_id)->first();

                if ($dbProduct) {
                    ProductToBasket::updateOrCreate([
                        'basketId' => $dbBasket->id,
                        'productId' => $dbProduct->id,
                    ], [
                        'count' => $product->quantity,
                        'price' => $dbProduct->price,
                    ]);
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function recountBasket()
    {
        $baskets = Basket::get();

        foreach ($baskets as $basket) {
            $totalPrice = 0;

            $products = $basket->products;

            foreach ($products as $product) {
                $totalPrice += $product->count * $product->price;
            }

            $basket->totalPrice = $totalPrice;
            $basket->save();
        }
    }
}
