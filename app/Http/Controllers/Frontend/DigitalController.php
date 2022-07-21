<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\DigitalService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DigitalController extends Controller
{
    public function index(Request $request, DigitalService $digitalService)
    {
        $data = $digitalService->getList($request->get('vendor', null));

        return Inertia::render('Digitals', $data);
    }
}
