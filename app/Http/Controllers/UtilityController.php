<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    public function PriceUpdate(Request $request, Utility $utility)
    {
        Utility::where('field', 'price')->update(['is_active' => false]);

        $user = auth()->user();

        $utility = new Utility();

        $utility->field = 'price';
        $utility->value = $request->input('amount');
        $utility->is_active = true;
        $utility->user_id = $user->id;
        $utility->save();

        return;
    }

    public function list()
    {
        return Utility::where('field', 'price')->sortBy('desc')->get();
    }
}
