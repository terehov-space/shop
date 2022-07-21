<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function getList(Request $request, OrderService $orderService)
    {
        $data = $orderService->getList($request->get('s', null));

        return Inertia::render('Orders', $data);
    }

    public function editPage($vendorId, Request $request, OrderService $orderService)
    {
        $data = $orderService->getById($vendorId);

        return Inertia::render('Order', $data);
    }

    public function store($vendorId, Request $request, OrderService $orderService)
    {
        $data = $orderService->store($vendorId, $request->only([
            'status',
        ]));

        if ($request->has('e') && (bool)$request->get('e')) {
            $listPath = '/admin/content/orders';
            return redirect($listPath);
        } else {
            return redirect("/admin/orders/{$data['item']['id']}");
        }
    }
}
