<?php

namespace App\Services\Frontend;

use App\Models\Carousel;
use App\Models\Product;
use App\Models\Section;

class CarouselService
{
    public function getList()
    {
        $items = Carousel::get();

        return [
            'items' => $items->map(function ($item) {
                $hasLink = false;
                $link = '';

                if ($item->model && $item->modelId) {
                    $hasLink = true;
                    $model = $item->relation;

                    switch ($item->model) {

                        case Section::class:
                            $link = "/catalog/{$model->code}";
                            break;
                        case Product::class:
                            $link = "/product/{$model->code}";
                            break;
                    }
                }

                return [
                    'image' => asset($item->image->path),
                    'mobileImage' => $item->mobile ? asset($item->mobile->path) : null,
                    'title' => $item->title,
                    'hasLink' => $hasLink,
                    'link' => $link,
                ];
            })
        ];
    }
}
