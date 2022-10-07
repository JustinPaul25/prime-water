<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    public function PriceUpdate(Request $request, Utility $utility)
    {
        $utility->update([
            'value' => $request->input('amount')
        ]);

        return;
    }
}
