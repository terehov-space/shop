<?php

namespace App\Services\Frontend;

use App\Models\Basket;
use App\Models\Product;
use App\Models\ProductToBasket;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Session;

class BasketService
{
    public function getBasket()
    {
        $ssid = Session::getId();

        $basket = Basket::where('code', '=', $ssid)
            ->whereNull('status')
            ->first();

        if (!$basket) {
            $basket = new Basket();
            $basket->code = $ssid;
            $basket->save();
        }

        $basket->refresh();

        $basketProducts = ProductToBasket::where('basketId', '=', $basket->id)
            ->get();

        return [
            'item' => [
                'id' => $basket->id,
                'code' => $basket->code,
                'totalPrice' => $basket->totalPrice,
            ],
            'items' => $basketProducts->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'title' => $item->product->title,
                    'image' => $item->product->image ? asset($item->product->image->path) : asset('/images/logo.svg'),
                    'count' => $item->count,
                    'price' => $item->price,
                ];
            }),
        ];
    }

    public function getOrder($orderId)
    {
        $ssid = Session::getId();

        $basket = Basket::where('id', '=', $orderId)
            ->whereNotNull('status')
            ->first();

        $basketProducts = ProductToBasket::where('basketId', '=', $basket->id)
            ->get();

        $status = 'Создан';

        switch ($basket->status) {
            case 'process':
                $status = 'В обработке';
                break;
            case 'ready':
                $status = 'Выполнен';
                break;
            default:
                break;
        }

        return [
            'item' => [
                'id' => $basket->id,
                'code' => $basket->code,
                'totalPrice' => $basket->totalPrice,
                'status' => $status,
            ],
            'items' => $basketProducts->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'title' => $item->product->title,
                    'image' => $item->product->image ? asset($item->product->image->path) : asset('/images/logo.svg'),
                    'count' => $item->count,
                    'price' => $item->price,
                ];
            }),
        ];
    }

    public function modifyBasket($productId, $quantity = null)
    {
        $product = Product::find($productId);

        $ssid = Session::getId();

        $basket = Basket::where('code', '=', $ssid)
            ->whereNull('status')
            ->first();

        if (!$basket) {
            $basket = new Basket();
            $basket->code = $ssid;
            $basket->save();
        }

        $basket->refresh();

        $basketProduct = ProductToBasket::where('basketId', '=', $basket->id)
            ->where('productId', '=', $productId)
            ->first();

        if ($quantity === 0) {
            $basketProduct->delete();
        } else {
            if (!$basketProduct) {
                $basketProduct = new ProductToBasket();
                $basketProduct->count = 0;
                $basketProduct->basketId = $basket->id;
            }

            $basketProduct->price = $product->price;
            $basketProduct->count = $quantity ?: $basketProduct->count + 1;
            $basketProduct->productId = $product->id;
            $basketProduct->save();

            $basket->refresh();
        }

        $totalPrice = 0;

        foreach ($basket->products as $ptb) {
            $totalPrice += $ptb->count * $ptb->price;
        }

        $basket->totalPrice = $totalPrice;
        $basket->save();

        return $this->getBasket();
    }

    public function storeOrder($orderData)
    {
        $ssid = Session::getId();

        $basket = Basket::where('code', '=', $ssid)
            ->whereNull('status')
            ->first();

        $basket->fill($orderData);
        $basket->status = 'new';
        $basket->save();

        $basketProducts = ProductToBasket::where('basketId', '=', $basket->id)
            ->get();

        return [
            'item' => [
                'id' => $basket->id,
                'code' => $basket->code,
                'totalPrice' => $basket->totalPrice,
            ],
            'items' => $basketProducts->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'title' => $item->product->title,
                    'image' => $item->product->image ? asset($item->product->image->path) : asset('/images/logo.svg'),
                    'count' => $item->count,
                    'price' => $item->price,
                ];
            }),
        ];
    }
}
