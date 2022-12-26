<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Reading;
use Illuminate\Http\Request;

class UsageController extends Controller
{
    public function usage()
    {
        return Inertia::render('Usage');
    }

    public function list(Request $request)
    {
        $transactions = Reading::query();

        if($request->filled('searchDate')) {
            $transactions->whereDate('created_at', '=', date($request->input('searchDate')));
        }

        if($request->filled('searchFrom') && $request->filled('searchTo')) {
            $to = date('Y-m-d', strtotime($request->input('searchTo'). ' + 1 days'));
            $transactions = $transactions->whereBetween('created_at', [date($request->input('searchFrom')), date($to)]);
        }

        if($request->filled('searchYear')) {
            $transactions = $transactions->whereYear('created_at', '=', $request->input('searchYear'));
        }

        return $transactions->get();
    }
}
