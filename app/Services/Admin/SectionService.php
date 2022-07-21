<?php

namespace App\Services\Admin;

use App\Models\Product;
use App\Models\Section;

class SectionService
{
    public function getList($search = null, $subSections = false)
    {
        $sections = Section::query();

        if ($search) {
            $sPhrase = '%' . $search . '%';
            $sections = $sections->where('title', 'like', $sPhrase);
        }

        if (!$subSections) {
            $sections = $sections->whereNull('sectionId');
        }

        $sections = $sections->paginate(10);

        return [
            'currentPage' => $sections->currentPage(),
            'lastPage' => $sections->lastPage(),
            'items' => $sections->map(function ($item) {
                $productsCount = $item->product->count() + $item->products->count();

                $subProductsCount = 0;

                if ($item->sections) {
                    foreach ($item->sections as $subSection) {
                        $subProductsCount += ($subSection->product->count() + $subSection->product->count());
                    }
                }

                $level = 1;

                if ($item->sectionId && $item->section) {
                    $level = 2;

                    if ($item->section->sectionId && $item->section->section) {
                        $level = 3;
                    }
                }

                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'title' => $item->title,
                    'image' => $item->image ? asset($item->image->path) : null,
                    'hasSections' => (bool)$item->sections->count(),
                    'countSections' => $item->sections->count(),
                    'countProducts' => "{$productsCount} ({$subProductsCount})",
                    'level' => $level,
                ];
            })
        ];
    }

    public function getListById($sectionId)
    {
        $section = Section::find($sectionId);

        $sections = Section::where('sectionId', '=', $sectionId)->paginate(10);

        return [
            'currentPage' => $sections->currentPage(),
            'lastPage' => $sections->lastPage(),
            'item' => [
                'id' => $section->id,
                'title' => $section->title,
                'sectionId' => $section->sectionId,
            ],
            'items' => $sections->map(function ($item) {
                $productsCount = Product::where('sectionId', '=', $item->id)->count();

                $subProductsCount = 0;

                if ($item->sections) {
                    $sectionsIds = $item->sections->pluck('id');
                    $subProductsCount = Product::whereIn('sectionId', $sectionsIds)->count();
                }

                $level = 2;

                if ($item->section->sectionId && $item->section->section) {
                    $level = 3;
                }

                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'title' => $item->title,
                    'image' => $item->image ? asset($item->image->path) : null,
                    'hasSections' => (bool)$item->sections->count(),
                    'countSections' => $item->sections->count(),
                    'countProducts' => "{$productsCount} ({$subProductsCount})",
                    'level' => $level,
                ];
            })
        ];
    }

    public function getById($sectionId, $parentId = null)
    {
        $response = [
            'item' => [
                'id' => $sectionId,
                'title' => null,
                'sectionId' => (int)$parentId,
                'code' => null,
                'showMain' => null,
            ],
        ];

        if ($sectionId !== 0) {
            $section = Section::find($sectionId);

            $response['item'] = [
                'id' => $section->id,
                'title' => $section->title,
                'code' => $section->code,
                'sectionId' => $section->sectionId,
                'showMain' => (int)$section->showMain,
                'imageId' => $section->imageId,
                'image' => $section->image ? asset($section->image->path) : null,
            ];
        }

        $response['sections'] = Section::get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
            ];
        });

        return $response;
    }

    public function store($sectionId, $data)
    {
        $section = Section::find($sectionId);

        if (!$section) {
            $section = new Section();
        }

        $section->fill($data);
        $section->save();
        $section->refresh();

        return [
            'item' => [
                'id' => $section->id,
                'title' => $section->title,
                'code' => $section->code,
                'sectionId' => $section->sectionId,
                'imageId' => $section->sectionId,
                'image' => $section->image ? asset($section->image->path) : null,
            ],
        ];
    }
}
