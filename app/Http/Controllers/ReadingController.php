<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reading;
use App\Models\Utility;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    public function store(Request $request)
    {
        $client = User::find($request->input('client_id'));
        $reading = Reading::where('client_id', $client->id)->latest()->first();
        $readMonthYear = $reading->created_at->month . '-' . $reading->created_at->year;
        $nowMonthYear = now()->month . '-' . now()->year;

        if ($reading->current_reading >= $request->input('reading'))
            return redirect()->back()->withErrors([
                'create' => 'ups, there was an error'
            ]);
        if ($readMonthYear === $nowMonthYear)
            return redirect()->back()->withErrors([
                'create' => 'ups, there was an error'
            ]);

        $account = $client->account;

        $price = Utility::where('field', 'price')->where('is_active', true)->first();

        $new_current = $request->input('reading');

        $reading_diff = $new_current - $account->prev_reading;

        $payment = $reading_diff * $price->value;

        $account->prev_reading = $account->current_reading;
        $account->prev_balance = $account->prev_balance + $account->current_charges;
        $account->current_reading = $new_current;
        $account->current_charges = $payment;
        $account->update();

        $reading = new Reading;
        $reading->client_id = $request->input('client_id');
        $reading->meterman_id = auth()->user()->id;
        $reading->prev_reading = $new_current - $reading_diff;
        $reading->current_reading = $new_current;
        $reading->price = $payment;
        $reading->save();

        return;
    }

    public function update(User $user, Request $request)
    {
        $auth = auth()->user();

        $reading = Reading::where('client_id', $user->id)->latest()->first();

        $current_rate = Utility::where('field', 'price')->where('is_active', true)->first();
        $cu_price = $current_rate->value;

        $account = $request->user->account;
        $prev = $account->prev_reading;
        $cur = $account->current_reading;
        $new_reading = $request->reading - $account->prev_reading;
        $new_charge = $new_reading * $cu_price;

        $account->current_reading = $request->reading;
        $account->current_charges = $new_charge;
        $account->save();

        $reading->message = "Current reading change by ".$auth->name." From: ".$reading->current_reading." To: ".$request->reading;
        $reading->current_reading = $request->reading;
        $reading->price = $new_charge;
        $reading->meterman_id = $auth->id;
        $reading->save();

        return $user;
    }

    public function clientLogs(User $user)
    {
        return Reading::where('client_id', $user->id)->get();
    }
}
