<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SectionController extends Controller
{
    public function getList(Request $request, SectionService $sectionService)
    {
        $data = $sectionService->getList($request->get('q', null), $request->get('s', false));

        return Inertia::render('Sections', $data);
    }

    public function getListBySection($sectionId, Request $request, SectionService $sectionService)
    {
        $data = $sectionService->getListById($sectionId);

        return Inertia::render('Sections', $data);
    }

    public function editPage($sectionId, Request $request, SectionService $sectionService)
    {
        $data = $sectionService->getById((int)$sectionId, $request->get('parent', null));

        return Inertia::render('Section', $data);
    }

    public function store($sectionId, Request $request, SectionService $sectionService)
    {
        $code = Str::slug($request->title);

        if ($request->sectionId) {
            $code .= "_{$request->sectionId}";
        }

        $request->request->add(['code' => $code]);

        $request->validate([
            'title' => 'required|min:5',
            'code' => "required|unique:sections,code,{$sectionId}",
        ]);

        $toFill = $request->only([
            'title',
            'sectionId',
            'imageId',
            'showMain',
        ]);

        $toFill['code'] = Str::slug($toFill['title']);
        $toFill['sectionId'] = $toFill['sectionId'] ? $toFill['sectionId'] : null;

        $data = $sectionService->store($sectionId, $toFill);

        Cache::tags(['products', 'sections'])->flush();

        if ($request->has('e') && (bool)$request->get('e')) {
            $listPath = '/admin/catalog/sections';

            if ($request->has('parent') && (bool)$request->get('parent')) {
                $listPath .= "/{$request->get('parent')}";
            }

            return redirect('/admin/catalog/sections');
        } else {
            return redirect("/admin/catalog/sections/{$data['item']['id']}/edit");
        }
    }
}
