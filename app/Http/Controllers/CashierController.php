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

        if($request->input('amount') > $account->prev_balance) {
            $remainingAmount = $request->input('amount') - $account->prev_balance;
            $newPrev = 0;
            $newCur = $account->current_charges - $remainingAmount;
        } else {
            $newPrev = $account->prev_balance - $request->input('amount');
            $newCur = $account->current_charges;
        }

        $account->update([
            'last_payment' => date("Y-m-d H:i:s", strtotime('now')),
            'current_charges' => $newCur,
            'prev_balance' => $newPrev,
        ]);

        Transaction::create([
            'client_id' => $request->input('id'),
            'cashier_id' => auth()->id(),
            'amount' => $request->input('amount'),
        ]);

        return Inertia::render('Payment');
    }
}
