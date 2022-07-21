<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Image;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function noModel(Request $request)
    {
        $filePath = $request->file('file')->store('public/uploads/images');

        $realPath = str_replace('public/uploads/images', 'uploads/images', $filePath);

        return response(asset($realPath));
    }

    public function image(Request $request)
    {
        $filePath = $request->file('image')->store('public/uploads/images');

        $realPath = str_replace('public/uploads/images', 'uploads/images', $filePath);

        $image = Image::updateOrCreate([
            'path' => $realPath,
        ], [

        ]);

        return response()->json([
            'image' => [
                'path' => asset($image->path),
                'id' => $image->id,
            ],
        ]);
    }

    public function file(Request $request)
    {
        $filePath = $request->file('file')->store('public/uploads/files');

        $realPath = str_replace('public/uploads/files', 'uploads/files', $filePath);

        $image = File::updateOrCreate([
            'path' => $realPath,
        ], [

        ]);

        return response()->json([
            'file' => [
                'path' => asset($image->path),
                'id' => $image->id,
                'title' => null,
            ],
        ]);
    }
}
