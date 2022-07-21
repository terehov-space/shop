<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PropertyService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropertyController extends Controller
{
    public function getList($sectionId, Request $request, PropertyService $propertyService)
    {
        $data = $propertyService->getListBySection($sectionId, $request->get('q', null));

        return Inertia::render('Properties', $data);
    }

    public function store($sectionId, Request $request, PropertyService $propertyService)
    {
        $propertyService->store($sectionId, $request->all());

        return redirect("/admin/catalog/sections/{$sectionId}/props");
    }

    public function editPage($sectionId, $propertyId, Request $request, PropertyService $propertyService)
    {
        $data = $propertyService->getOptionsBySection($sectionId, $propertyId);

        return Inertia::render('Property', $data);
    }

    public function storeOption($sectionId, $propertyId, Request $request, PropertyService $propertyService)
    {
        $propertyService->storeOption($sectionId, $propertyId, $request->all());

        return redirect("/admin/catalog/sections/{$sectionId}/props/{$propertyId}");
    }

    public function deleteProperty($sectionId, $propertyId, Request $request, PropertyService $propertyService)
    {
        $propertyService->deleteProperty($propertyId);

        return redirect("/admin/catalog/sections/{$sectionId}/props");
    }

    public function deleteOption($sectionId, $propertyId, $optionId, Request $request, PropertyService $propertyService)
    {
        $propertyService->deleteOption($optionId);

        return redirect("/admin/catalog/sections/{$sectionId}/props/{$propertyId}");
    }
}
