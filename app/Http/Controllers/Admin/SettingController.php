<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function getItem(Request $request)
    {
        $setting = Setting::first();

        return Inertia::render('Setting', [
            'setting' => $setting,
        ]);
    }

    public function storeItem(Request $request)
    {
        $setting = Setting::first();
        $setting->fill($request->all());
        $setting->save();

        return redirect('/admin/settings');
    }
}
