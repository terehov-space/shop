<?php

namespace App\Console\Commands\Cometa;

use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use voku\helper\HtmlDomParser;

class Prices extends Command
{
    private Client $client;

    private $searchPath = '/search?q=';

    private $baseUrl = 'https://www.comet-a.ru';

    public function __construct()
    {
        parent::__construct();

        $this->client = new Client();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cometa:sync {sync?}';

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
        switch ($this->argument('sync')) {
            case 'links':
                $this->syncLinks();
                break;
            case 'price':
                $this->syncPrices();
                break;
            default:
                $this->error('Неверный параметр');
                break;
        }

        return 0;
    }

    private function syncLinks()
    {
        $products = Product::whereNotNull('vendorCode')
            ->select(['vendorCode'])
            ->distinct()
            ->get();

        $this->output->progressStart($products->count());

        foreach ($products as $product) {
            $this->output->progressAdvance();
            $response = $this->client->get($this->baseUrl . $this->searchPath . $product->vendorCode);
            $html = (string)$response->getBody();

            $dom = HtmlDomParser::str_get_html($html);

            $catalog = $dom->findOne('.catalog-content__wrap');
            $cards = $catalog->findMulti('.item-card');

            foreach ($cards as $card) {
                $htmlVendorCode = $card->findOne('.item-card-art');
                if ($htmlVendorCode->innertext == "Артикул: {$product->vendorCode}") {
                    $href = $card->findOne('.item-card-info__name')->getAttribute('href');

                    $syncProducts = Product::where('vendorCode', '=', $product->vendorCode)
                        ->get();

                    foreach ($syncProducts as $syncProduct) {
                        $syncProduct->syncCometa = $href;
                        $syncProduct->save();
                    }
                }
            }
        }

        $this->output->progressFinish();
    }

    private function syncPrices()
    {
        $products = Product::whereNotNull('syncCometa')
            ->get();

        $this->output->progressStart($products->count());

        foreach ($products as $product) {
            try {
                $response = $this->client->get($this->baseUrl . $product->syncCometa);
                $html = (string)$response->getBody();
                $dom = HtmlDomParser::str_get_html($html);

                $productCard = $dom->findOne('.prod-card-section');

                if ($productCard->innerhtml) {
                    $form = $productCard->findOne('.fi-form');

                    $priceFound = false;
                    $price = null;

                    foreach ($form->childNodes() as $child) {
                        if ($child->tag == 'meta' && $child->hasAttribute('itemprop') && $child->getAttribute('itemprop') == 'price') {
                            $price = $child->getAttribute('content');

                            if ($price) {
                                $priceFound = true;
                                $product->price = $price;
                                $product->showPrice = true;
                                $product->save();
                                $this->output->progressAdvance();
                            }
                            break;
                        }
                    }

                    if (!$priceFound) {
                        $product->showPrice = false;
                        $product->save();
                        $this->output->progressAdvance();
                    }
                }
            } catch (\Throwable $e) {

            }
        }

        $this->output->progressFinish();

        Cache::tags(['products', 'sections'])->flush();
    }
}
