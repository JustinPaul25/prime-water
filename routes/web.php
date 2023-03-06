<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UsageController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\ChangePasswordController;

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
if (App::environment('production')) {
    URL::forceScheme('https');
}

Route::get('', function () {
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
Route::get('/all-clients', [ClientsController::class, 'all'])->middleware(['auth', 'verified'])->name('all-clients');
Route::get('/switch-status/{user}', [ClientsController::class, 'switchStatus'])->middleware(['auth', 'verified'])->name('switch.status');

//reading
Route::post('/reading', [ReadingController::class, 'store'])->middleware(['auth', 'verified'])->name('reading.create');

//utilities
Route::put('/utilities-price/{utility}', [UtilityController::class, 'priceUpdate'])->middleware(['auth', 'verified'])->name('price.update');

//Reports
Route::get('/reports', [ReportsController::class, 'index'])->middleware(['auth', 'verified'])->name('reports');
Route::get('/transactions', [TransactionsController::class, 'list'])->middleware(['auth', 'verified'])->name('transaction.list');
Route::get('/client-transactions/{user}', [TransactionsController::class, 'clientList'])->middleware(['auth', 'verified'])->name('transaction.client');

//Cashier
Route::get('/payments', [CashierController::class, 'index'])->middleware(['auth', 'verified'])->name('payments');
Route::post('/pay-bill', [CashierController::class, 'pay'])->middleware(['auth', 'verified'])->name('pay.bill');

Route::get('/change-password', [UserController::class, 'changePassword'])->middleware(['auth', 'verified'])->name('change.password.form');
Route::post('/change-password', [UserController::class, 'updatePassword'])->middleware(['auth', 'verified'])->name('change.password');
Route::get('/send-sms/{user}', [SMSController::class, 'notify'])->middleware(['auth', 'verified'])->name('send-notification');
Route::get('/send-bill-duedate/{user}', [SMSController::class, 'bill'])->middleware(['auth', 'verified'])->name('send-bill-duedate');
Route::get('/notify-all', [SMSController::class, 'notifyAllUser'])->middleware(['auth', 'verified'])->name('notify-all');

Route::get('/usage', [UsageController::class, 'usage'])->middleware(['auth', 'verified'])->name('usage');
Route::get('/usage/list', [UsageController::class, 'list'])->middleware(['auth', 'verified'])->name('usage.list');

Route::post('/user-change-password', [ChangePasswordController::class, 'change'])->name('user.change.password');

require __DIR__.'/auth.php';
