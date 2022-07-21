<?php

namespace App\Services\Admin;

use App\Models\Vendor;
use Illuminate\Support\Str;

class VendorService
{
    public function getList($search = null)
    {
        $vendors = Vendor::query();

        if ($search) {
            $vendors = $vendors->where('title', 'like', "%{$search}%");
        }

        $vendors = $vendors->paginate(10);

        return [
            'currentPage' => $vendors->currentPage(),
            'lastPage' => $vendors->lastPage(),
            'items' => $vendors->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'code' => $item->code,
                ];
            }),
        ];
    }

    public function getById($vendorId)
    {
        $response = [
            'item' => [
                'id' => $vendorId,
                'title' => null,
                'code' => null,
                'imageId' => null,
                'image' => null,
            ],
        ];

        if ($vendorId > 0) {

            $vendor = Vendor::find($vendorId);

            $response['item'] = [
                'id' => $vendor->id,
                'title' => $vendor->title,
                'code' => $vendor->code,
                'imageId' => $vendor->imageId,
                'image' => $vendor->image ? asset($vendor->image->path) : null,
            ];
        }

        return $response;
    }

    public function store($vendorId, $data)
    {
        $vendor = Vendor::find($vendorId);

        if (!$vendor) {
            $vendor = new Vendor();
        }

        if (!$vendor->code) {
            $data['code'] = Str::slug($data['title']);
        }

        $vendor->fill($data);
        $vendor->save();
        $vendor->refresh();

        return [
            'item' => [
                'id' => $vendor->id,
                'title' => $vendor->title,
                'code' => $vendor->code,
                'imageId' => $vendor->imageId,
                'image' => $vendor->image ? asset($vendor->image->path) : null,
            ],
        ];
    }
}
