<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Home', [
            'title' => 'Home',
        ]);
    }

    public function about(Request $request)
    {
        return Inertia::render('About', [
            'title' => 'About',
            'test' => 'test',
        ]);
    }
}
