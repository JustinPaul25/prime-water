<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        $income = Transaction::select(
            DB::raw('extract(year from created_at) as year'),
            DB::raw('extract(month from created_at) as month'),
            DB::raw('sum(amount) as amount'),
        )
            ->where(DB::raw('date(created_at)'), '>=', "2010-01-01")
            ->groupBy('year')
            ->groupBy('month')
            ->get()
            ->toArray();

        return Inertia::render('Reports', ['income_data' => $income]);
    }
}
