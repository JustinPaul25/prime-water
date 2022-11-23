<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
    {
        return Inertia::render('Payment');
    }

    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
        ]);

        $account = Account::where('client_id', $request->input('id'))->first();
        
        if($account->prev_balance > $request->input('amount')) {
            $current_charges = $account->current_charges;
            $prev_balance = $account->prev_balance - $request->input('amount');
        } else {
            $prev_balance = 0;
            $current_charges = $account->current_charges - ($request->input('amount') - $account->prev_balance);
        }

        $account->update([
            'current_charges' => $current_charges,
            'prev_balance' => $prev_balance,
        ]);

        Transaction::create([
            'client_id' => $request->input('id'),
            'cashier_id' => auth()->id(),
            'amount' => $request->input('amount'),
        ]);

        return Inertia::render('Payment');
    }
}
