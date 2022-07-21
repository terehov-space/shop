<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\ProductService;
use App\Services\Frontend\SectionsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function index(Request $request, SectionsService $sectionsService)
    {
        $sections = $sectionsService->getList((bool)$request->get('cache'));

        return Inertia::render('Catalog', [
            'sections' => $sections,
        ]);
    }

    public function sectionPage($sectionCode, Request $request, SectionsService $sectionsService)
    {
        $data = $sectionsService->getByCode($sectionCode, $request->get('page'), (bool)$request->get('cache'));

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Section', [
            'data' => $data,
        ]);
    }

    public function productPage($productCode, Request $request, ProductService $productService)
    {
        $product = $productService->getByCode($productCode, (bool)$request->get('cache'));

        return Inertia::render('Product', [
            'product' => $product,
        ]);
    }

    public function sections(Request $request, SectionsService $sectionsService)
    {
        return response()->json($sectionsService->getList((bool)$request->get('cache')));
    }

    public function sectionByCode($sectionCode, Request $request, SectionsService $sectionsService)
    {
        return response()->json($sectionsService->getByCode($sectionCode, $request->get('page'), (bool)$request->get('cache')));
    }

    public function productByCode($productCode, Request $request, ProductService $productService)
    {
        return response()->json($productService->getByCode($productCode, (bool)$request->get('cache')));
    }

    public function search(Request $request, ProductService $productService)
    {
        $data = $productService->search($request->get('q', null));

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Search', $data);
    }
}
