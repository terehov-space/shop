<?php

namespace App\Services\Admin;

use App\Models\ProductToSection;
use App\Models\Section;
use App\Models\Product;
use App\Models\Vendor;
use GuzzleHttp\Psr7\Request;

class GroupMovingService
{
    public function getList($filter = [])
    {
        $products = Product::query();
        if (!empty($filter['q']) || !empty($filter['sectionId']) || !empty($filter['secondIds']) || !empty($filter['thirdIds'])) {
            if (!empty($filter['q'])) {
                $products = $products->where('title', 'like', "%{$filter['q']}%")
                    ->orWhere('vendorCode', 'like', "%{$filter['q']}%");
            }

            if (!empty($filter['sectionId'])) {
                $products = $products->where('sectionId', '=', $filter['sectionId']);
            }

            $subSectionIds = [];

            if (!empty($filter['secondIds'])) {
                if (!is_array($filter['secondIds'])) {
                    $filter['secondIds'] = [
                        $filter['secondIds'],
                    ];
                }
                $subSectionIds = $filter['secondIds'];
            }

            if (!empty($filter['thirdIds'])) {
                if (!is_array($filter['thirdIds'])) {
                    $filter['thirdIds'] = [
                        $filter['thirdIds'],
                    ];
                }
                $subSectionIds = array_merge($subSectionIds, $filter['thirdIds']);
            }

            if (!empty($subSectionIds)) {
                $products = $products->whereHas('sections', function ($query) use ($subSectionIds) {
                    return $query->whereIn('sectionId', $subSectionIds);
                });
            }
        } else {
            $products = $products->whereNull('id');
        }

//        print_r($products->toSql());
//        die();

        $productList = $products->get();

        $sections = Section::get();

        return [
            'items' => $productList->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'vendorCode' => $item->vendorCode,
                ];
            }),
            'sections' => $sections->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'sectionId' => $item->sectionId,
                ];
            }),
        ];
    }

    public function storeSections($data)
    {
        foreach ($data['items'] as $pId) {
            $product = Product::find($pId);

            $product->sectionId = $data['sectionId'];
            $product->save();

            $ptsIds =  [];

            if (!empty($data['secondIds'])) {
                $ptsIds[] = $data['secondIds'];
            }

            if (!empty($data['thirdIds'])) {
                $ptsIds[] = $data['thirdIds'];
            }

            $pts = ProductToSection::query();

            if (!empty($ptsIds)) {
                $pts = $pts->whereNotIn('sectionId', $ptsIds);
            }

            $pts = $pts->where('productId', '=', $product->id)->get();

            foreach ($pts as $pt) {
                $pt->delete();
            }

            foreach ($ptsIds as $ptsId) {
                ProductToSection::updateOrCreate([
                    'productId' => $product->id,
                    'sectionId' => $ptsId,
                ], []);
            }
        }
    }

    public function storeVendors($data)
    {
        foreach ($data['items'] as $pId) {
            $product = Product::find($pId);

            $vendor = Vendor::find($data['vendorId']);

            $product->vendorId = $vendor->id;
            $product->save();
        }
    }
}
