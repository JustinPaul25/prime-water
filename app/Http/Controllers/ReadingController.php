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

        $reading_diff = $new_current - $account->current_reading;

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
        $reading = Reading::where('client_id', $user->id)->latest()->first();

        $current_rate = Utility::where('field', 'price')->where('is_active', true)->first();
        $cu_price = $current_rate->value;

        $account = $request->user->account;
        $prev = $account->prev_reading;
        $cur = $account->current_reading;
        $old_consumed = $cur - $prev;
        $old_charge = $old_consumed * $cu_price;
        $new_charge = $request->reading * $cu_price;

        $account->current_reading = $request->reading;
        $account->current_charges = $new_charge;
        $account->save();

        $reading->current_reading = $request->reading;
        $reading->price = $new_charge;
        $reading->save();

        return $user;
    }
}
