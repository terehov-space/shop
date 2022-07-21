<?php

namespace App\Http\Middleware;

use App\Models\Basket;
use App\Models\Setting;
use App\Services\Frontend\SectionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    private SectionsService $sectionsService;

    public function __construct(SectionsService $sectionsService)
    {
        $this->sectionsService = $sectionsService;
    }

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * @param string $rootView
     */
    public function rootView(Request $request): string
    {
        if (str_contains($request->route()->getPrefix(), 'admin')) {
            return 'admin';
        }

        return 'app';
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        $basket = null;

        $basket = Basket::where('code', '=', Session::getId())
            ->whereNull('status')
            ->first();

        if ($basket) {
            $basket = $basket->append('products');
        }

        // global params settings, phones, logo
        return array_merge(parent::share($request), [
            'sectionLinks' => $this->sectionsService->getLinks(),
            'settings' => Setting::first(),
            'basket' => $basket,
        ]);
    }
}
