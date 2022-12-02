<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Reading;
use App\Models\Utility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientsController extends Controller
{
    public function list()
    {
        $clients = Reading::with(['client'])->get();

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

        $price = Utility::find(1);

        $new_current = $request->input('reading') + $account->current_reading;

        $reading_diff = $request->input('reading') - $account->current_reading;

        $payment = $reading_diff * $price->value;

        $account->prev_reading = $account->current_reading;
        $account->current_reading = $new_current;
        $account->current_charges = $payment;
        $account->update();

        $reading = new Reading;
        $reading->client_id = $request->input('id');
        $reading->meterman_id = 2;
        $reading->prev_reading = $new_current - $reading_diff;
        $reading->current_reading = $new_current;
        $reading->price = $payment;
        $reading->save();

        return;
    }
}
