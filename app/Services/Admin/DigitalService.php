<?php

namespace App\Services\Admin;

use App\Models\Digital;

class DigitalService
{
    public function getList($search = null)
    {
        $digitals = Digital::query();

        if ($search) {
            $digitals = $digitals->where('title', 'like', "%{$search}%");
        }

        $digitals = $digitals->paginate(10);

        return [
            'currentPage' => $digitals->currentPage(),
            'lastPage' => $digitals->lastPage(),
            'items' => $digitals->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                ];
            }),
        ];
    }

    public function getById($digitalId)
    {
        $response = [
            'item' => [
                'id' => $digitalId,
                'title' => null,
                'imageId' => null,
                'image' => null,
                'fileId' => null,
                'file' => null,
            ],
        ];

        if ($digitalId > 0) {

            $digital = Digital::find($digitalId);

            $response['item'] = [
                'id' => $digital->id,
                'title' => $digital->title,
                'code' => $digital->code,
                'imageId' => $digital->imageId,
                'image' => $digital->image ? asset($digital->image->path) : null,
                'fileId' => $digital->fileId,
                'file' => $digital->file ? asset($digital->file->path) : null,
            ];
        }

        return $response;
    }

    public function store($digitalId, $data)
    {
        $digital = Digital::find($digitalId);

        if (!$digital) {
            $digital = new Digital();
        }

        $digital->fill($data);
        $digital->save();
        $digital->refresh();

        return [
            'item' => [
                'id' => $digital->id,
                'title' => $digital->title,
                'code' => $digital->code,
                'imageId' => $digital->imageId,
                'image' => $digital->image ? asset($digital->image->path) : null,
                'fileId' => $digital->fileId,
                'file' => $digital->file ? asset($digital->file->path) : null,
            ],
        ];
    }
}
