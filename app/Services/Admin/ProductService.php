<?php

namespace App\Services\Admin;

use App\Models\File;
use App\Models\FileToProduct;
use App\Models\Image;
use App\Models\ImageToProduct;
use App\Models\Product;
use App\Models\ProductToSection;
use App\Models\Property;
use App\Models\PropertyOption;
use App\Models\PropertyToProduct;
use App\Models\Section;
use App\Models\Vendor;
use Illuminate\Support\Str;

class ProductService
{
    public function getList($sectionId = null, $noSection = null, $search = null, $perPage = null)
    {
        $showPerPage = $perPage ?: 10;

        $products = Product::query();

        if ($sectionId) {
            $products = $products->where('sectionId', '=', $sectionId);
        } elseif ($noSection) {
            $products = $products->whereNull('sectionId');
        }

        if ($search) {
            $products = $products->where('title', 'like', "%{$search}%")
                ->orWhere('vendorCode', 'like', "%{$search}%");
        }

        $products = $products->paginate($showPerPage);

        $sections = Section::get()->map(function ($item) {
            $productsCount = Product::where('sectionId', '=', $item->id)->count();

            return [
                'id' => $item->id,
                'title' => "{$item->title} ({$productsCount})",
                'sectionId' => $item->sectionId,
            ];
        });

        $vendors = Vendor::get()->map(function ($item) {
            return [
                'title' => $item->title,
                'id' => $item->id,
            ];
        });

        return [
            'currentPage' => $products->currentPage(),
            'lastPage' => $products->lastPage(),
            'items' => $products->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'code' => $item->code,
                    'price' => $item->price,
                    'hasErrors' => !$item->section || $item->section->sectionId,
                ];
            }),
            'sections' => $sections,
            'vendors' => $vendors,
        ];
    }

    public function getById($productId)
    {
        $response = [
            'item' => [
                'id' => $productId,
                'title' => null,
                'code' => null,
                'vendorCode' => null,
                'vendorId' => null,
                'description' => null,
                'price' => null,
                'showPrice' => null,
                'priceEur' => null,
                'priceUsd' => null,
                'updateEur' => null,
                'updateUsd' => null,
                'syncCometa' => null,
                'showMain' => null,
                'seoTitle' => null,
                'seoDescription' => null,
                'imageId' => null,
                'sectionId' => null,
                'image' => null,
                'onOrder' => null,
            ],
            'properties' => [],
        ];

        if ($productId !== 0) {
            $product = Product::find($productId);

            $response['item'] = [
                'title' => $product->title,
                'code' => $product->code,
                'vendorCode' => $product->vendorCode,
                'vendorId' => $product->vendorId,
                'description' => $product->description,
                'price' => $product->price,
                'showPrice' => $product->showPrice,
                'priceEur' => $product->priceEur,
                'priceUsd' => $product->priceUsd,
                'updateEur' => $product->updateEur,
                'updateUsd' => $product->updateUsd,
                'syncCometa' => $product->syncCometa,
                'showMain' => $product->showMain,
                'seoTitle' => $product->seoTitle,
                'seoDescription' => $product->seoDescription,
                'imageId' => $product->imageId,
                'sectionId' => $product->sectionId,
                'onOrder' => $product->onOrder,
                'image' => $product->image ? asset($product->image->path) : null,
                'hasErrors' => !$product->section || $product->section->sectionId,
            ];

            if ($product->sectionId) {
                $properties = Property::where('sectionId', '=', $product->sectionId)->get();

                $response['properties'] = $properties->map(function ($item) use ($product) {
                    $options = PropertyOption::where('sectionId', '=', $item->sectionId)
                        ->where('propertyId', '=', $item->id)
                        ->get();

                    $value = PropertyToProduct::where('propertyId', '=', $item->id)
                        ->where('productId', '=', $product->id)
                        ->first();

                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'sectionId' => $item->sectionId,
                        'options' => $options->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'value' => $item->value,
                            ];
                        }),
                        'value' => $value ? $value->optionId : null,
                    ];
                });
            }
        }

        $response['sections'] = Section::get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'sectionId' => $item->sectionId,
            ];
        });

        $response['vendors'] = Vendor::get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
            ];
        });

        $response['images'] = ImageToProduct::where('productId', '=', $productId)->get()->map(function ($item) {
            return [
                'id' => $item->image->id,
                'itpId' => $item->id,
                'image' => asset($item->image->path),
            ];
        });

        $response['files'] = FileToProduct::where('productId', '=', $productId)->get()->map(function ($item) {
            return [
                'id' => $item->file->id,
                'itpId' => $item->id,
                'file' => asset($item->file->path),
                'title' => $item->file->title,
            ];
        });

        $pts = ProductToSection::where('productId', '=', $productId)->get();

        $second = $pts->filter(function ($item) {
            return $item->section->level == 2;
        });

        $third = $pts->filter(function ($item) {
            return $item->section->level == 3;
        });

        $response['secondIds'] = $second->pluck('sectionId');
        $response['thirdIds'] = $third->pluck('sectionId');

        return $response;
    }

    public function store($productId, $data, $images = [], $files = [], $sections = [], $properties = [])
    {
        $product = Product::find($productId);

        if (!$product) {
            $product = new Product();
        }

        if ($product->code) {
            unset($data['code']);
        } else {
            $data['code'] = Str::slug($data['title']);
        }

        $product->fill($data);
        $product->save();
        $product->refresh();

        $this->processImages($product, $images);
        $this->processFiles($product, $files);
        $this->processSections($product, $sections);
        $this->processProperties($productId, $product, $properties);

        return [
            'item' => [
                'id' => $product->id,
                'title' => $product->title,
                'code' => $product->code,
                'vendorCode' => $product->vendorCode,
                'description' => $product->description,
                'price' => $product->price,
                'showPrice' => $product->showPrice,
                'priceEur' => $product->priceEur,
                'priceUsd' => $product->priceUsd,
                'updateEur' => $product->updateEur,
                'updateUsd' => $product->updateUsd,
                'showMain' => $product->showMain,
                'seoTitle' => $product->seoTitle,
                'seoDescription' => $product->seoDescription,
                'imageId' => $product->imageId,
                'sectionId' => $product->sectionId,
                'image' => $product->image,
                'hasErrors' => !$product->section || $product->section->sectionId,
            ],
        ];
    }

    private function processImages($product, $images)
    {
        $imageTpIds = [];
        foreach ($images as $image) {
            if (!empty($image['itpId'])) {
                $imageTpIds[] = $image['itpId'];
            }
        }

        $dbImages = ImageToProduct::where('productId', '=', $product->id);

        if (!empty($imageTpIds)) {
            $dbImages = $dbImages->whereNotIn('id', $imageTpIds);
        }

        $dbImages = $dbImages->get();

        foreach ($dbImages as $dbImage) {
            $dbImage->delete();
        }

        if (!empty($images)) {
            foreach ($images as $image) {
                if (!empty($image['itpId'])) {
                    continue;
                }

                $newImage = new Image();
                $newImage->path = $image['image'];
                $newImage->save();

                ImageToProduct::updateOrCreate([
                    'imageId' => $newImage->id,
                    'productId' => $product->id,
                ], []);
            }
        }
    }

    private function processFiles($product, $files)
    {
        $fileTpIds = [];
        foreach ($files as $file) {
            if (!empty($file['ftpId'])) {
                $fileTpIds[] = $file['ftpId'];
            }
        }

        $dbFiles = FileToProduct::where('productId', '=', $product->id);

        if (!empty($fileTpIds)) {
            $dbFiles = $dbFiles->whereNotIn('id', $fileTpIds);
        }

        $dbFiles = $dbFiles->get();

        foreach ($dbFiles as $dbFile) {
            $dbFile->delete();
        }

        if (!empty($files)) {
            foreach ($files as $file) {
                if (!empty($file['ftpId'])) {
                    continue;
                }

                $newFile = new File();
                $newFile->path = $file['file'];
                $newFile->title = $file['title'];
                $newFile->save();
                $newFile->refresh();

                FileToProduct::updateOrCreate([
                    'fileId' => $newFile->id,
                    'productId' => $product->id,
                ], []);
            }
        }
    }

    private function processSections($product, $sections)
    {
        $dbSections = ProductToSection::where('productId', '=', $product->id);

        if (!empty($dbSections)) {
            $dbSections = $dbSections->whereNotIn('sectionId', $sections);
        }

        $dbSections = $dbSections->get();

        foreach ($dbSections as $dbSection) {
            $dbSection->delete();
        }

        if (!empty($sections)) {
            foreach ($sections as $section) {
                ProductToSection::updateOrCreate([
                    'sectionId' => $section,
                    'productId' => $product->id,
                ], []);
            }
        }
    }

    private function processProperties($productId, $product, $properties)
    {
        if ($productId > 0) {
            foreach ($properties as $property) {
                PropertyToProduct::updateOrCreate([
                    'productId' => $product->id,
                    'propertyId' => $property['id'],
                    'sectionId' => $property['sectionId'],
                ], [
                    'optionId' => $property['value'],
                ]);
            }
        }
    }

    public function deleteItem($productId)
    {
        $product = Product::find($productId);

        if ($product) {
            $product->delete();
        }
    }
}
