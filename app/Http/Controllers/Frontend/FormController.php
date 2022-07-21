<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        $data = $request->only([
            'productId',
            'name',
            'phone',
            'email',
            'comment',
        ]);

        $form = new Form();
        $form->fill($data);
        $form->save();

        return response()->json([
            'status' => 'success',
        ]);
    }
}
