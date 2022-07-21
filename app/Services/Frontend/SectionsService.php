<?php

namespace App\Services\Frontend;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SectionsService
{
    public function getIndex($clearCache = false)
    {
        if ($clearCache) {
            Cache::forget('section.index');
        }

        $sectionsList = [];

        if (Cache::has('section.index')) {
            $sectionsList = Cache::tags(['sections', 'products'])->get('section.index');
        } else {
            $items = Section::productable()->where('showMain', '=', true)->limit(8)->inRandomOrder()->get();

            $sectionsList = [
                'items' => $items->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'code' => $item->code,
                        'image' => $item->image ? asset($item->image->path) : asset('/images/logo.svg'),
                    ];
                })
            ];

            Cache::tags(['sections', 'products'])->add('section.index', $sectionsList, 7200);
        }

        return $sectionsList;
    }

    public function getList($clearCache = false)
    {
        if ($clearCache) {
            Cache::forget('sections.list');
        }

        $sectionsList = [];

        if (Cache::has('sections.list')) {
            $sectionsList = Cache::get('sections.list');
        } else {
            $items = Section::productable()->whereNull('sectionId')->get()->append('sections');

            $sectionsList = [
                'items' => $items->map(function ($item) {
                    $subSections = [];

                    if ($item->sections) {
                        $subSectionIds = $item->sections->pluck('id');

                        $subSections = Section::productable()->whereIn('id', $subSectionIds)
                            ->get()
                            ->map(function ($item) {
                                return [
                                    'title' => $item->title,
                                    'code' => $item->code,
                                ];
                            });
                    }


                    return [
                        'title' => $item->title,
                        'code' => $item->code,
                        'image' => $item->image ? asset($item->image->path) : asset('/images/logo.svg'),
                        'sections' => $subSections,
                    ];
                }),
            ];

            Cache::tags(['sections', 'products'])->add('sections.list', $sectionsList, 7200);
        }

        return $sectionsList;
    }

    public function getByCode($code, $page = null, $clearCache = false)
    {
        if ($clearCache) {
            Cache::forget("section.detail.{$code}");
        }

        $sectionDetail = [];

        if (Cache::has("section.detail.{$code}") && !$page) {
            $sectionDetail = Cache::get("section.detail.{$code}");
        } else {
            $section = Section::productable()->where('code', '=', $code)->firstOrFail();

            $subSections = [];

            if ($section->sections) {
                $sectionIds = $section->sections->pluck('id');

                $subSections = Section::productable()->whereIn('id', $sectionIds)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'title' => $item->title,
                            'code' => $item->code,
                        ];
                    });
            }

            $productsList = [];
            $products = null;

            if (!$section->sections->count() && !$section->section) {
                $productIds = DB::table('product_to_sections')
                    ->select('productId')
                    ->where('sectionId', '=', $section->id)
                    ->union(
                        DB::table('products')
                            ->select('id as productId')
                            ->where('sectionId', '=', $section->id)
                    )
                    ->get();

                $products = Product::whereHas('section', function ($query) {
                    return $query->whereNull('sectionId');
                })
                    ->whereIn('id', $productIds->pluck('productId'))
                    ->paginate(15);


                $productsList = $products->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'code' => $item->code,
                        'image' => $item->image ? asset($item->image->path) : asset('/images/logo.svg'),
                        'price' => $item->showPrice && $item->price ? $item->price : null,
                        'vendorCode' => $item->vendorCode,
                    ];
                });
            } else {
                $sectionIds = [];
                $sectionIds[] = $section->id;
                if ($section->sections) {
                    $sectionIds = [...$section->sections->pluck('id'), $section->id];
                }

                $productIds = DB::table('product_to_sections')
                    ->select('productId')
                    ->whereIn('sectionId', $sectionIds)
                    ->distinct()
                    ->get();

                $products = Product::whereIn('id', $productIds->pluck('productId'))
                    ->paginate(15);

                $productsList = $products->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'code' => $item->code,
                        'image' => $item->image ? asset($item->image->path) : asset('/images/logo.svg'),
                        'price' => $item->showPrice && $item->price ? $item->price : null,
                        'vendorCode' => $item->vendorCode,
                    ];
                });
            }

            $parentSection = null;

            if ($section->section) {
                $parentSection = [
                    'title' => $section->section->title,
                    'code' => $section->section->code,
                ];

                if ($section->section->sections) {
                    $sectionIds = $section->section->sections->pluck('id');

                    $parentSection['sections'] = Section::productable()->whereIn('id', $sectionIds)
                        ->get()
                        ->map(function ($item) {
                            return [
                                'title' => $item->title,
                                'code' => $item->code,
                            ];
                        });
                }
            }

            $seoTitle = "Купить {$section->title} у официального дилера с доставкой по Москве, Екатеринбургу и России";//$section->title;

            if ($section->seoTitlePostfix) {
                $seoTitle .= " - {$section->seoTitlePostfix}";
            }

            $seoDescription = "{$section->title} большой выбор, только оригинальный товар и комплектующие напрямую со склада поставщика";

            if ($section->seoDescription) {
                $seoTitle = $section->seoDescription;
            }

            $sectionDetail = [
                'item' => [
                    'title' => $section->title,
                    'code' => $section->code,
                    'sections' => $subSections,
                    'section' => $parentSection,
                    'seoTitle' => $seoTitle,
                    'seoDescription' => $seoDescription,
                ],
                'totalPages' => $products ? $products->lastPage() : -1,
                'currentPage' => $products ? $products->currentPage() : -1,
                'items' => $productsList,
            ];

            if (!$page) {
                Cache::tags(['sections', 'products'])->add("section.detail.{$code}", $sectionDetail);
            }
        }

        return $sectionDetail;
    }

    public function getLinks()
    {
        $sectionLinks = [];

        $sections = Section::productable()->whereNull('sectionId')->get();

        foreach ($sections as $section) {
            $sectionLink = [
                'title' => $section->title,
                'code' => $section->code,
                'sections' => $section->sections ? $this->getSubLinks($section) : [],
            ];

            $sectionLinks[] = $sectionLink;
        }

        return $sectionLinks;
    }

    private function getSubLinks($section)
    {
        $sectionIds = $section->sections->pluck('id');
        $subSections = Section::productable()
            ->whereIn('id', $sectionIds)
            ->get();
        $sectionSubLinks = [];

        foreach ($subSections as $subSection) {
            $sectionSubLink = [
                'title' => $subSection->title,
                'code' => $subSection->code,
                'sections' => $subSection->sections ? $this->getSubLinks($subSection) : [],
            ];

            $sectionSubLinks[] = $sectionSubLink;
        }


        return $sectionSubLinks;
    }
}
