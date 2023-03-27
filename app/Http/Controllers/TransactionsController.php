<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function list(Request $request)
    {
        $transactions = Transaction::query();

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

        if($request->filled('searchMonth')) {
            $transactions = $transactions->whereMonth('created_at', '=', $request->input('searchMonth'));
        }

        return $transactions->get();
    }

    public function calculateIncom(Request $request)
    {
        # code...
    }

    public function clientList(User $user)
    {
        $transactions = Transaction::where('client_id', $user->id)->get();

        return $transactions;
    }
}
