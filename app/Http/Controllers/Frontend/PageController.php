<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function pageByCode($pageCode, Request $request)
    {
        $response = [
            'item' => [
                'title' => null,
                'body' => null,
                'seoTitle' => null,
                'seoDescription' => null,
            ]
        ];

        $pageParts = collect(explode('/', $pageCode));

        $main = $pageParts->first();

        $mainPage = Page::where('code', '=', $main)->first();

        if ($mainPage && $pageParts->count() > 1) {
            $curPart = null;

            foreach ($pageParts as $part) {
                $curPart = Page::where('pageId', '=', $curPart->id ?? null)
                    ->where('code', '=', $part)
                    ->first();
            }

            if ($curPart) {
                $seoTitle = $curPart->title;

                if ($curPart->seoTitlePostfix) {
                    $seoTitle .= " - {$curPart->seoTitlePostfix}";
                }

                $response['item'] = [
                    'title' => $curPart->title,
                    'body' => $curPart->body,
                    'seoTitle' => $seoTitle,
                    'seoDescription' => $curPart->seoDescription,
                ];
            }
        } elseif ($mainPage) {
            if ($mainPage->openLink) {

                $seoTitle = $mainPage->title;

                if ($mainPage->seoTitlePostfix) {
                    $seoTitle .= " - {$mainPage->seoTitlePostfix}";
                }

                $response['item'] = [
                    'title' => $mainPage->title,
                    'body' => $mainPage->body,
                    'seoTitle' => $seoTitle,
                    'seoDescription' => $mainPage->seoDescription,
                ];
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }

        return Inertia::render('Page', $response);
    }
}
