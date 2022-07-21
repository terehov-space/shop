<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class PriceRecount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price:recount';

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
        $priceJson = file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js');
        $priceArr = json_decode($priceJson, true);

        $eur = $priceArr['Valute']['EUR'];
        $usd = $priceArr['Valute']['USD'];

        $this->info($eur['Value']);
        $this->info($usd['Value']);

        $products = Product::where('updateUsd', '=', true)
            ->whereNotNull('priceUsd')
            ->get();

        foreach ($products as $product) {
            $product->price = $product->priceUsd * $usd['Value'];
            $product->save();
        }

        $products = Product::where('updateEur', '=', true)
            ->whereNotNull('priceEur')
            ->get();

        foreach ($products as $product) {
            $product->price = $product->priceUsd * $eur['Value'];
            $product->save();
        }

        return 0;
    }
}
