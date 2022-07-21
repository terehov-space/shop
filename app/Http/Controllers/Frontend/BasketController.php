<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\BasketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class BasketController extends Controller
{
    public function basketPage(Request $request, BasketService $basketService)
    {
        $data = $basketService->getBasket();

        return Inertia::render('Basket', $data);
    }

    public function getOrder(Request $request, BasketService $basketService)
    {
        $data = $basketService->getOrder($request->get('order'));

        return Inertia::render('Order', $data);
    }

    public function newOrder(Request $request, BasketService $basketService)
    {
        $data = $basketService->getBasket();

        return Inertia::render('NewOrder', $data);
    }

    public function modifyBasket(Request $request, BasketService $basketService)
    {
        $productId = $request->post('productId');

        return response()->json($basketService->modifyBasket($productId, $request->post('count', null)));
    }

    public function storeOrder(Request $request, BasketService $basketService)
    {
        $orderData = $request->only([
            'email',
            'phone',
        ]);

        $data = $basketService->storeOrder($orderData);

        $nssid = $request->session()->regenerate(true);
        Session::setId($nssid);

        return response()->json($data);
    }
}
