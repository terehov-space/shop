<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\CarouselService;
use App\Services\Frontend\ProductService;
use App\Services\Frontend\SectionsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function index(Request $request, CarouselService $carouselService, SectionsService $sectionsService, ProductService $productService)
    {
        $carousel = $carouselService->getList();
        $sections = $sectionsService->getIndex((bool)$request->get('cache'));
        $products = $productService->getIndex((bool)$request->get('cache'));

        return Inertia::render('Index', [
            'carousel' => $carousel,
            'sections' => $sections,
            'products' => $products,
            'seoTitle' => '',
            'seoDescription' => '',
        ]);
    }

    public function carousel(Request $request, CarouselService $carouselService)
    {
        return response()->json($carouselService->getList());
    }

    public function section(Request $request, SectionsService $sectionsService)
    {
        return response()->json($sectionsService->getIndex());
    }

    public function product(Request $request, ProductService $productService)
    {
        return response()->json($productService->getIndex((bool)$request->get('cache')));
    }
}
