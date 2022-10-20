<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if(auth()->user()->isAdmin()) {
            return Inertia::render('Dashboard/Dashboard');
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
