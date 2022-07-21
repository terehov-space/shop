<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PageService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function getList(Request $request, PageService $pageService)
    {
        $data = $pageService->getList($request->get('q', null));

        return Inertia::render('Pages', $data);
    }

    public function editPage($pageId, Request $request, PageService $pageService)
    {
        $data = $pageService->getById($pageId);

        return Inertia::render('Page', $data);
    }

    public function store($vendorId, Request $request, PageService $pageService)
    {
        $request->validate([
            'title' => 'required|min:5',
            'code' => 'required|min:5',
        ]);

        $data = $pageService->store($vendorId, $request->only([
            'title',
            'code',
            'pageId',
            'body',
            'seoTitlePostfix',
            'seoDescription',
        ]));

        if ($request->has('e') && (bool)$request->get('e')) {
            $listPath = '/admin/content/pages';
            return redirect($listPath);
        } else {
            return redirect("/admin/content/pages/{$data['item']['id']}");
        }
    }
}
