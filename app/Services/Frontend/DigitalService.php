<?php

namespace App\Services\Frontend;

use App\Models\Digital;
use App\Models\Vendor;

class DigitalService
{
    public function getList($vendor = null)
    {
        $digitals = Digital::get();
        $vendors = Vendor::get();

        return [
            'items' => $digitals->map(function ($item) {
                return [
                    'title' => $item->title,
                    'image' => $item->image ? asset($item->image->path) : asset('/images/logo.svg'),
                    'file' => $item->file ? asset($item->file->path) : null,
                    'vendorId' => $item->vendorId,
                ];
            }),
            'vendors' => $vendors,
        ];
    }
}
