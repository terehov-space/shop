<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\FormService;
use App\Services\Admin\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormController extends Controller
{
    public function getList(Request $request, FormService $formService)
    {
        $data = $formService->getList();

        return Inertia::render('Forms', $data);
    }

    public function editPage($vendorId, Request $request, FormService $formService)
    {
        $data = $formService->getById($vendorId);

//        return Inertia::render('Form', $data);
    }
}
