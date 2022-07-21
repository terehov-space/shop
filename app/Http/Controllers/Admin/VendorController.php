<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\VendorService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorController extends Controller
{
    public function getList(Request $request, VendorService $vendorService)
    {
        $data = $vendorService->getList($request->get('s', null));

        return Inertia::render('Vendors', $data);
    }

    public function editPage($vendorId, Request $request, VendorService $vendorService)
    {
        $data = $vendorService->getById($vendorId);

        return Inertia::render('Vendor', $data);
    }

    public function store($vendorId, Request $request, VendorService $vendorService)
    {
        $request->validate([
            'title' => 'required|min:2',
        ]);

        $data = $vendorService->store($vendorId, $request->only([
            'title',
            'code',
            'imageId',
        ]));

        if ($request->has('e') && (bool)$request->get('e')) {
            $listPath = '/admin/content/vendors';
            return redirect($listPath);
        } else {
            return redirect("/admin/content/vendors/{$data['item']['id']}");
        }
    }
}
