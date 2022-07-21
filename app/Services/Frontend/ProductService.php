<?php

namespace App\Services\Frontend;

use App\Models\Product;
use App\Models\ProductToSection;
use App\Models\Property;
use App\Models\PropertyOption;
use App\Models\PropertyToProduct;
use App\Models\Section;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function getIndex($cache = false)
    {
        if ($cache) {
            Cache::forget('product.index');
        }

        $productList = [];

        if (Cache::has('product.index')) {
            $productList = Cache::get('product.index');
        } else {
            $products = Product::where('showMain', '=', true)->get();

            $productList = [
                'items' => $products->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'code' => $item->code,
                        'onOrder' => $item->onOrder,
                        'image' => $item->image ? asset($item->image->path) : asset('/images/logo.svg'),
                        'price' => $item->showPrice && $item->price ? $item->price : null,
                        'vendorCode' => $item->vendorCode,
                    ];
                })
            ];

            Cache::tags(['products'])->add('section.index', $productList, 900);
        }

        return $productList;
    }

    public function getByCode($productCode, $cache = false)
    {
        if ($cache) {
            Cache::forget('product.detail');
        }

        $product = [];

        if (Cache::has('product.detail')) {
            $product = Cache::get('product.detail');
        } else {
            $dbProduct = Product::where('code', '=', $productCode)->firstOrFail();

            if ($dbProduct) {
                $sections = [];
                $properties = [];

                if ($dbProduct->sectionId) {
                    $dbSection = Section::find($dbProduct->sectionId);

                    $sections[] = [
                        'title' => $dbSection->title,
                        'code' => $dbSection->code,
                    ];

                    $dbSubSectionX = ProductToSection::where('productId', '=', $dbProduct->id)
                        ->first();

                    if ($dbSubSectionX) {
                        $dbSubSection = Section::find($dbSubSectionX->sectionId);

                        $sections[] = [
                            'title' => $dbSubSection->title,
                            'code' => $dbSubSection->code,
                        ];
                    }

                    $propertyToProduct = PropertyToProduct::where('productId', '=', $dbProduct->id)
                        ->where('sectionId', '=', $dbProduct->sectionId)
                        ->whereNotNull('optionId')
                        ->get();


                    if ($propertyToProduct->count() > 0) {
                        $properties = $propertyToProduct->map(function ($item) {
                            return [
                                'title' => $item->property->title,
                                'value' => $item->option->value,
                            ];
                        });
                    }
                }

                $gallery = [];

                if ($dbProduct->images) {
                    $gallery = $dbProduct->images->map(function ($item) {
                        return [
                            'path' => asset($item->image->path),
                        ];
                    });
                }

                $files = [];

                if ($dbProduct->files) {
                    $files = $dbProduct->files->map(function ($item) {
                        return [
                            'id' => $item->file->id,
                            'title' => $item->file->title,
                            'path' => asset($item->file->path),
                        ];
                    });
                }

                $seoTitle = "Купить {$dbProduct->title} с доставкой по Москве, Екатеринбургу и России";

                if ($dbProduct->seoTitle) {
                    $seoTitle .= " - {$dbProduct->seoTitle}";
                }

                $seoDescription = "{$dbProduct->title} {$dbProduct->vendorCode} заказать у проверенного поставщика с доставкой по всей России.";//$dbProduct->description;

                if ($dbProduct->seoDescription) {
                    $seoDescription = $dbProduct->seoDescription;
                }

                $vendorImage = null;
                $vendorTitle = null;

                if ($dbProduct->vendor) {
                    $vendorTitle = $dbProduct->vendor->title;

                    if ($dbProduct->vendor->image) {
                        $vendorImage = asset($dbProduct->vendor->image->path);
                    }
                }

                $product = [
                    'id' => $dbProduct->id,
                    'title' => $dbProduct->title,
                    'description' => trim(str_replace('\n', '', $dbProduct->description ?? '')),
                    'code' => $dbProduct->code,
                    'image' => $dbProduct->image ? asset($dbProduct->image->path) : asset('/images/logo.svg'),
                    'images' => $gallery,
                    'vendorImage' => $vendorImage,
                    'vendorTitle' => $vendorTitle,
                    'files' => $files,
                    'price' => $dbProduct->showPrice && $dbProduct->price ? $dbProduct->price : null,
                    'vendorCode' => $dbProduct->vendorCode,
                    'onOrder' => $dbProduct->onOrder,
                    'sections' => $sections,
                    'properties' => $properties,
                    'seoTitle' => $seoTitle,
                    'seoDescription' => $seoDescription,
                ];
            }
        }

        return $product;
    }

    public function search($search = null)
    {
        $products = Product::where('title', 'like', "%{$search}%")
            ->orWhere('vendorCode', 'like', "%{$search}%")
            ->paginate(15);

        return [
            'lastPage' => $products ? $products->lastPage() : -1,
            'currentPage' => $products ? $products->currentPage() : -1,
            'items' => $products->map(function ($item) {
                return [
                    'title' => $item->title,
                    'code' => $item->code,
                    'onOrder' => $item->onOrder,
                    'image' => $item->image ? asset($item->image->path) : asset('/images/logo.svg'),
                    'price' => $item->showPrice && $item->price ? $item->price : null,
                    'vendorCode' => $item->vendorCode,
                ];
            }),
        ];
    }
}
