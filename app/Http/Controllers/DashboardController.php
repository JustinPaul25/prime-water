<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Reading;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if(auth()->user()->isAdmin()) {
            $income = Transaction::select(
                DB::raw('year(created_at) as year'),
                DB::raw('month(created_at) as month'),
                DB::raw('sum(amount) as amount'),
            )
                ->where(DB::raw('date(created_at)'), '>=', "2010-01-01")
                ->groupBy('year')
                ->groupBy('month')
                ->get()
                ->toArray();

            $reading = Reading::select(
                DB::raw('year(created_at) as year'),
                DB::raw('month(created_at) as month'),
                DB::raw('sum(current_reading) as readings'),
            )
                ->where(DB::raw('date(created_at)'), '>=', "2010-01-01")
                ->groupBy('year')
                ->groupBy('month')
                ->get()
                ->toArray();
            
            return Inertia::render('Dashboard/Dashboard', ['income' => $income, 'reading' => $reading]);
        }

        if(auth()->user()->isClient()) {
            return Inertia::render('Dashboard/DashboardClient');
        }

        if(auth()->user()->isMeterman()) {
            return Inertia::render('Dashboard/DashboardMeterman');
        }

        return redirect(route('payments'));
    }
}
