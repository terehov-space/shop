<?php

namespace App\Services\Admin;

use App\Models\Basket;
use App\Models\Page;
use App\Models\ProductToBasket;

class OrderService
{
    public function getList()
    {
        $basket = Basket::whereNotNull('status')
            ->paginate(10);

        return [
            'lastPage' => $basket->lastPage(),
            'currentPage' => $basket->currentPage(),
            'items' => $basket->map(function ($item) {
                $basketProducts = ProductToBasket::where('basketId', '=', $item->id)
                    ->get();

                $status = 'Создан';

                switch ($item->status) {
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
                    'id' => $item->id,
                    'code' => $item->code,
                    'totalPrice' => $item->totalPrice,
                    'status' => $status,
                    'products' => $basketProducts->count(),
                ];
            })
        ];
    }

    public function getById($PageId)
    {
        $basket = Basket::find($PageId);

        $basketProducts = ProductToBasket::where('basketId', '=', $basket->id)
            ->get();

        return [
            'item' => [
                'id' => $basket->id,
                'code' => $basket->code,
                'totalPrice' => $basket->totalPrice,
                'status' => $basket->status,
                'phone' => $basket->phone,
                'email' => $basket->email,
            ],
            'items' => $basketProducts->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'title' => $item->product->title,
                    'image' => $item->product->image ? asset($item->product->image->path) : null,
                    'count' => $item->count,
                    'price' => $item->price,
                ];
            }),
        ];
    }

    public function store($PageId, $data)
    {
        $Page = Basket::find($PageId);

        $Page->fill($data);
        $Page->save();

        return [
            'item' => [
                'id' => $Page->id,
                'title' => $Page->title,
            ],
        ];
    }
}
