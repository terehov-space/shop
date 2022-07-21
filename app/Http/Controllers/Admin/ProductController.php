<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function getList(Request $request, ProductService $productService)
    {
        $data = $productService->getList($request->get('section', null), $request->get('n', null), $request->get('q', null), $request->get('perPage', null));

        return Inertia::render('Products', $data);
    }

    public function editPage($productId, Request $request, ProductService $productService)
    {
        $data = $productService->getById((int)$productId);

        return Inertia::render('Product', $data);
    }

    public function store($productId, Request $request, ProductService $productService)
    {
        $request->validate([
            'product.title' => 'required|min:5',
            'product.code' => "unique:products,code,{$productId}",
        ]);

        $product = $request->post('product');
        $images = $request->post('images', []);
        $files = $request->post('files', []);
        $sections = $request->post('sections', []);
        $properties = $request->post('properties', []);

        $data = $productService->store($productId, $product, $images, $files, $sections, $properties);

        Cache::tags(['products'])->flush();

        if ($request->has('e') && (bool)$request->get('e')) {
            return redirect('/admin/catalog/products');
        } else {
            return redirect("/admin/catalog/products/{$data['item']['id']}");
        }
    }

    public function delete($productId, Request $request, ProductService $productService)
    {
        $productService->deleteItem($productId);

        return redirect('/admin/catalog/products');
    }
}
