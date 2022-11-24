<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(Auth::check()) {
        return redirect(route('dashboard'));
    } else {
        return redirect(route('login'));
    }
});

Route::get('/offline', function () {
    return view('vendor.laravelpwa.offline');
});

//dashbard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

//staff
Route::get('/staff', [StaffController::class, 'index'])->middleware(['auth', 'verified'])->name('staff');
Route::post('/staff', [StaffController::class, 'create'])->middleware(['auth', 'verified'])->name('staff.create');
Route::put('/staff/{user}', [StaffController::class, 'update'])->middleware(['auth', 'verified'])->name('staff.update');
Route::delete('/{user}/staff', [StaffController::class, 'destroy'])->middleware(['auth', 'verified'])->name('staff.delete');
Route::get('/staff-list', [StaffController::class, 'list'])->middleware(['auth', 'verified'])->name('staff.list');

//client
Route::get('/client', [ClientsController::class, 'index'])->middleware(['auth', 'verified'])->name('client');
Route::post('/client', [ClientsController::class, 'create'])->middleware(['auth', 'verified'])->name('client.create');
Route::get('/client/{user}', [ClientsController::class, 'show'])->middleware(['auth', 'verified'])->name('client.show');
Route::put('/client/{user}', [ClientsController::class, 'update'])->middleware(['auth', 'verified'])->name('client.update');
Route::delete('/{user}/client', [ClientsController::class, 'destroy'])->middleware(['auth', 'verified'])->name('client.delete');
Route::get('/client-list', [ClientsController::class, 'list'])->middleware(['auth', 'verified'])->name('client.list');
Route::get('/client-data/{user}', [ClientsController::class, 'data'])->middleware(['auth', 'verified'])->name('client.data');
Route::get('/client/{user}/profile', [ClientsController::class, 'profile'])->middleware(['auth', 'verified'])->name('client.profile');

//reading
Route::post('/reading', [ReadingController::class, 'store'])->middleware(['auth', 'verified'])->name('reading.create');

//utilities
Route::put('/utilities-price/{utility}', [UtilityController::class, 'priceUpdate'])->middleware(['auth', 'verified'])->name('price.update');

//Reports
Route::get('/reports', [ReportsController::class, 'index'])->middleware(['auth', 'verified'])->name('reports');
Route::get('/transactions', [TransactionsController::class, 'list'])->middleware(['auth', 'verified'])->name('transaction.list');

//Cashier
Route::get('/payments', [CashierController::class, 'index'])->middleware(['auth', 'verified'])->name('payments');
Route::post('/pay-bill', [CashierController::class, 'pay'])->middleware(['auth', 'verified'])->name('pay-bill');

require __DIR__.'/auth.php';
