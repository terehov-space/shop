<?php

namespace App\Services\Admin;

use App\Models\Basket;
use App\Models\Form;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductToBasket;

class FormService
{
    public function getList()
    {
        $basket = Form::paginate(10);

        return [
            'lastPage' => $basket->lastPage(),
            'currentPage' => $basket->currentPage(),
            'items' => $basket->map(function ($item) {
                $product = Product::find($item->productId);

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'email' => $item->email,
                    'phone' => $item->phone,
                    'product' => $product->id,
                ];
            })
        ];
    }

    public function getById($PageId)
    {
        $basket = Form::find($PageId);

        $product = Product::where('id', '=', $basket->productId)->get();

        return [
            'item' => [
                'id' => $basket->id,
                'name' => $basket->name,
                'email' => $basket->email,
                'phone' => $basket->phone,
            ],
            'items' => $product->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'image' => $item->image ? asset($item->image->path) : null,
                    'count' => $item->count,
                    'price' => $item->price,
                ];
            }),
        ];
    }
}
