<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Reading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsageController extends Controller
{
    public function usage()
    {
        $usage = Reading::select(
            DB::raw('extract(year from created_at) as year'),
            DB::raw('extract(month from created_at) as month'),
            DB::raw('sum(current_reading) as readings'),
        )
            ->where(DB::raw('date(created_at)'), '>=', "2010-01-01")
            ->groupBy('year')
            ->groupBy('month')
            ->get()
            ->toArray();

        return Inertia::render('Usage', ['usage_data' => $usage]);
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
