<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\GroupMovingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GroupMovingController extends Controller
{
    public function index(Request $request, GroupMovingService $groupMovingService)
    {
        $data = $groupMovingService->getList($request->all());

        return Inertia::render('GroupMoving', $data);
    }

    public function storeSections(Request $request, GroupMovingService $groupMovingService)
    {
        $data = $request->all();

        $groupMovingService->storeSections($data);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function storeVendors(Request $request, GroupMovingService $groupMovingService)
    {
        $data = $request->all();

        $groupMovingService->storeVendors($data);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
