<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DigitalService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DigitalController extends Controller
{
    public function getList(Request $request, DigitalService $vendorService)
    {
        $data = $vendorService->getList($request->get('s', null));

        return Inertia::render('Digitals', $data);
    }

    public function editPage($vendorId, Request $request, DigitalService $vendorService)
    {
        $data = $vendorService->getById($vendorId);

        return Inertia::render('Digital', $data);
    }

    public function store($vendorId, Request $request, DigitalService $vendorService)
    {
        $request->validate([
            'title' => 'required|min:5',
        ]);

        $data = $vendorService->store($vendorId, $request->only([
            'title',
            'imageId',
            'fileId',
            'vendorId',
        ]));

        if ($request->has('e') && (bool)$request->get('e')) {
            $listPath = '/admin/content/digitals';
            return redirect($listPath);
        } else {
            return redirect("/admin/content/digitals/{$data['item']['id']}");
        }
    }
}
