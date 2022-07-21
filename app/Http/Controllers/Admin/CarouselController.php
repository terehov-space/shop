<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CarouselService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarouselController extends Controller
{
    public function getList(Request $request, CarouselService $vendorService)
    {
        $data = $vendorService->getList($request->get('s', null));

        return Inertia::render('Carousels', $data);
    }

    public function editPage($vendorId, Request $request, CarouselService $vendorService)
    {
        $data = $vendorService->getById($vendorId);

        return Inertia::render('Carousel', $data);
    }

    public function store($vendorId, Request $request, CarouselService $vendorService)
    {
        $request->validate([
            'title' => 'required|min:5',
        ]);

        $data = $vendorService->store($vendorId, $request->only([
            'title',
            'imageId',
            'mobileImage',
            'model',
            'modelId',
        ]));

        if ($request->has('e') && (bool)$request->get('e')) {
            $listPath = '/admin/content/carousels';
            return redirect($listPath);
        } else {
            return redirect("/admin/content/carousels/{$data['item']['id']}");
        }
    }
}
