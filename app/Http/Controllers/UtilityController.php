<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    public function PriceUpdate(Request $request, Utility $utility)
    {
        $utility = new Utility();

        $utility->field = 'price';
        $utility->value = $request->input('amount');
        $utility->save();

        return;
    }

    public function list()
    {
        return Utility::where('field', 'price')->sortBy('desc')->get();
    }
}
