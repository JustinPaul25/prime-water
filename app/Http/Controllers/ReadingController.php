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
        $account = $client->account;

        $price = Utility::find(1);

        $reading_diff = $request->input('reading') - $account->current_reading;

        $payment = $reading_diff * $price->value;

        $account->prev_reading = $account->current_reading;
        $account->current_reading = $request->input('reading');
        $account->current_charges = $payment;
        $account->update();

        $reading = new Reading;
        $reading->client_id = $request->input('client_id');
        $reading->meterman_id = auth()->user()->id;
        $reading->prev_reading = $request->input('reading') - $reading_diff;
        $reading->current_reading = $request->input('reading');
        $reading->price = $payment;
        $reading->save();

        return;
    }
}
