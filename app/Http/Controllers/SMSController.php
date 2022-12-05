<?php

namespace App\Http\Controllers;

use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    public function notify(User $user)
    {
        $basic  = new \Vonage\Client\Credentials\Basic("fc8342f4", "Zym3dVQtAi3SqOWe");
        $client = new \Vonage\Client($basic);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS("639099026782", 'NEXMO', 'Notice of disconnection')
        );

        return 'success';
    }
}
