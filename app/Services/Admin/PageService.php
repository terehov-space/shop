<?php

namespace App\Services\Admin;

use App\Models\Page;

class PageService
{
    public function getList($search = null)
    {
        $Pages = Page::query();

        if ($search) {
            $Pages = $Pages->where('title', 'like', "%{$search}%");
        }

        $Pages = $Pages->paginate(10);

        return [
            'currentPage' => $Pages->currentPage(),
            'lastPage' => $Pages->lastPage(),
            'items' => $Pages->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                ];
            }),
        ];
    }

    public function getById($PageId)
    {
        $response = [
            'item' => [
                'title' => null,
                'body' => null,
                'code' => null,
                'pageId' => null,
            ],
        ];


        if ($PageId > 0) {
            $Page = Page::find($PageId);

            $response['item'] = [
                'id' => $Page->id,
                'title' => $Page->title,
                'code' => $Page->code,
                'pageId' => $Page->pageId,
                'body' => $Page->body,
                'seoTitlePostfix' => $Page->seoTitlePostfix,
                'seoDescription' => $Page->seoDescription,
            ];
        }

        $pages = Page::get();

        $response['sections'] = $pages->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
            ];
        });

        return $response;
    }

    public function store($PageId, $data)
    {
        $Page = Page::find($PageId);

        if (!$Page) {
            $Page = new Page();
        }

        $Page->fill($data);
        $Page->save();
        $Page->refresh();

        return [
            'item' => [
                'id' => $Page->id,
                'title' => $Page->title,
            ],
        ];
    }
}
