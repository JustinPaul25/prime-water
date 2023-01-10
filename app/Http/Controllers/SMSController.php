<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SMSController extends Controller
{
    public function notify(User $user)
    {
        $message = 'Hello '.$user->first_name.' '.$user->last_name.' This is a notice of disconnetion from WBS. Current Bill: '.$user->account->curent_charges.'.';

        // $basic  = new \Vonage\Client\Credentials\Basic("91feaea9", "VoyeRquAsliUvY6V");
        // $client = new \Vonage\Client($basic);

        // $response = $client->sms()->send(
        //     new \Vonage\SMS\Message\SMS("639293194425", 'NEXMO', $message)
        // );

        $response = Http::get('http://MingSms.mingming13.repl.co?phone=' . '09934451453' . '&message=' . $message . '&key=wbs_system');
        //$send = json_encode(file_get_contents('https://MingSms.mingming13.repl.co?phone=' . '09934451453' . '&message=' . $message . '&key=wbs_system'));

        return $response;
    }
}
