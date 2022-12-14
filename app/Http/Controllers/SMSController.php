<?php

namespace App\Http\Controllers;

use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    public function notify(User $user)
    {
        $basic  = new \Vonage\Client\Credentials\Basic("91feaea9", "VoyeRquAsliUvY6V");
        $client = new \Vonage\Client($basic);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS("639293194425", 'NEXMO', 'This is Notice of disconnection From WBS')
        );

        return 'success';
    }
}
