<?php

namespace App\Services\Admin;

use App\Models\Carousel;
use App\Models\Product;
use App\Models\Section;

class CarouselService
{
    public function getList($search = null)
    {
        $carousels = Carousel::query();

        if ($search) {
            $carousels = $carousels->where('title', 'like', "%{$search}%");
        }

        $carousels = $carousels->paginate(10);

        return [
            'currentPage' => $carousels->currentPage(),
            'lastPage' => $carousels->lastPage(),
            'items' => $carousels->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                ];
            }),
        ];
    }

    public function getById($carouselId)
    {
        $response = [
            'item' => [
                'title' => null,
                'imageId' => null,
                'image' => null,
                'mobileImage' => null,
                'mobileImagePath' => null,
                'model' => null,
                'modelId' => null,
            ],
        ];


        if ($carouselId > 0) {
            $carousel = Carousel::find($carouselId);

            $response['item'] = [
                'id' => $carousel->id,
                'title' => $carousel->title,
                'code' => $carousel->code,
                'imageId' => $carousel->imageId,
                'image' => $carousel->image ? asset($carousel->image->path) : null,
                'mobileImage' => $carousel->mobileImage,
                'mobileImagePath' => $carousel->mobile ? asset($carousel->mobile->path) : null,
                'model' => $carousel->model,
                'modelId' => $carousel->modelId,
            ];
        }

        $sections = Section::productable()
            ->select([
                'id',
                'title',
            ])
            ->get();

        $products = Product::whereNotNull('sectionId')
            ->select([
                'id',
                'title',
            ])
            ->get();

        $response['sections'] = $sections;
        $response['products'] = $products;

        return $response;
    }

    public function store($carouselId, $data)
    {
        $carousel = Carousel::find($carouselId);

        if (!$carousel) {
            $carousel = new Carousel();
        }

        $carousel->fill($data);
        $carousel->save();
        $carousel->refresh();

        return [
            'item' => [
                'id' => $carousel->id,
                'title' => $carousel->title,
                'imageId' => $carousel->imageId,
                'image' => $carousel->image ? asset($carousel->image->path) : null,
                'mobileImage' => $carousel->mobileImage,
                'mobileImagePath' => $carousel->mobile ? asset($carousel->mobile->path) : null,
            ],
        ];
    }
}
