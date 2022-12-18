<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use Twilio\Rest\Client;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    public function notify(User $user)
    {
        $message = 'Hello '.$user->first_name.' '.$user->last_name.' This is a notice of disconnetion from WBS. Current Bill: '.$user->account->curent_charges.'.';

        $basic  = new \Vonage\Client\Credentials\Basic("91feaea9", "VoyeRquAsliUvY6V");
        $client = new \Vonage\Client($basic);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS("639293194425", 'NEXMO', $message)
        );

        return 'success';
    }
}
