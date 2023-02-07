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
                DB::raw('extract(year from created_at) as year'),
                DB::raw('extract(month from created_at) as month'),
                DB::raw('sum(amount) as amount'),
            )
                ->where(DB::raw('date(created_at)'), '>=', "2010-01-01")
                ->groupBy('year')
                ->groupBy('month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get()
                ->toArray();

            $reading = Reading::select(
                DB::raw('extract(year from created_at) as year'),
                DB::raw('extract(month from created_at) as month'),
                DB::raw('sum(current_reading) as readings'),
            )
                ->where(DB::raw('date(created_at)'), '>=', "2010-01-01")
                ->groupBy('year')
                ->groupBy('month')
                ->get()
                ->toArray();

            foreach($income as $key => $data) {
                $income[$key]['key'] = $key+1;
                $income[$key]['yearmonth'] = $data['month'] < 10
                    ? $data['year'] . '0' . $data['month']
                    : $data['year'] . $data['month'];
            }

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
