<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Reading;
use App\Models\Utility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ReadingLog;

class ClientsController extends Controller
{
    public function list(Request $request)
    {
        $clients = User::query();

        if($request->filled('search')) {
            $search = $request->input('search');
            $clients = $clients->where(function($q) use ($search){
                $q->where('first_name', 'LIKE', '%'.$search.'%')
                ->orWhere('middle_name', 'LIKE', '%'.$search.'%')
                ->orWhere('last_name', 'LIKE', '%'.$search.'%')
                ->orWhere('username', 'LIKE', '%'.$search.'%');
            });
        }

        $clients = $clients->role(['Client'])->get();

        return response()->json(['clients' => $clients]);
    }

    public function data(User $user)
    {
        return $user;
    }

    public function store(Request $request)
    {
        $client = User::find($request->input('id'));
        $account = $client->account;

        $reading = Reading::where('client_id', $client->id)->latest()->first();
        if($reading) {
            $readMonthYear = $reading->created_at->month . '-' . $reading->created_at->year;
            $nowMonthYear = now()->month . '-' . now()->year;

            if($reading->current_reading >= $request->input('reading'))
                return;

            if ($readMonthYear === $nowMonthYear)
                return;
        }

        $price = Utility::latest()->first();

        $new_current = $request->input('reading');

        $reading_diff = $new_current - $account->current_reading;

        $payment = $reading_diff * $price->value;

        $account->prev_reading = $account->current_reading;
        $account->prev_balance = $account->current_charges + $account->prev_balance;
        $account->current_reading = $new_current;
        $account->current_charges = $payment;
        $account->update();

        $reading = new Reading;
        $reading->client_id = $request->input('id');
        $reading->meterman_id = $request->input('meterman_id');
        $reading->prev_reading = $new_current - $reading_diff;
        $reading->current_reading = $new_current;
        $reading->cum_price = $price->value;
        $reading->price = $payment;
        $reading->save();

        $log = new ReadingLog();
        $log->changer_id = $request->input('meterman_id');
        $log->client_id = $client->id;
        $log->message = "Meterman Reading -> Previous Reading ".$reading->prev_reading." Current Reading ".$reading->current_reading;
        $log->save();

        return;
    }
}
