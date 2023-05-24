<?php

namespace App\Http\Controllers;

use App\Models\User;
use ClickSend\Configuration;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;

class SMSController extends Controller
{

    public function notify(User $user)
    {
        $balance = $user->account->prev_balance + $user->account->current_charges;
        $balance = number_format($balance, 2);
        $string = $user->contact_no;
        $number = substr_replace($string, "+63", 0, 1);
        $message = 'Hello '.$user->first_name.' '.$user->last_name.' This is a notice of disconnection from WBS. Current Bill: ₱'.$user->account->current_charges.', Previous Balance: ₱'.$user->account->prev_balance.', Total Balance: ₱'.$balance;

        $client = new Client(config('twilio.account_sid'), config('twilio.auth_token'));

        $message = $client->messages->create(
            $number,
            [
                'from' => config('twilio.from'),
                'body' => $message
            ]
        );

        return response()->json([
            'message' => 'SMS sent successfully.',
            'data' => $message
        ]);
    }

    public function notifyAllUser() {
        $client = new Client(config('twilio.account_sid'), config('twilio.auth_token'));
        $users = User::role(['Client'])->get();
        foreach($users as $user) {
            if($user->account->month_last_payment >= 3) {
                $balance = $user->account->prev_balance + $user->account->current_charges;
                $balance = number_format($balance, 2);
                $string = $user->contact_no;
                $number = substr_replace($string, "+63", 0, 1);
                $message = 'Hello '.$user->first_name.' '.$user->last_name.' This is a notice of disconnection from WBS. Current Bill: ₱'.$user->account->current_charges.', Previous Balance: ₱'.$user->account->prev_balance.', Total Balance: ₱'.$balance;

                $message = $client->messages->create(
                    $number,
                    [
                        'from' => config('twilio.from'),
                        'body' => $message
                    ]
                );
            }
        }

        return response()->json([
            'message' => 'SMS sent successfully.',
            'data' => $message
        ]);
    }

    public function bill(User $user)
    {
        $date = $user->account->due_date;
        $balance = $user->account->prev_balance + $user->account->current_charges;
        $balance = number_format($balance, 2);
        $string = $user->contact_no;
        $number = substr_replace($string, "+63", 0, 1);
        $message = 'Hello '.$user->first_name.' '.$user->last_name.' Here is your bill this month and the due date will be on ' . $date . '. Current Bill: ₱'.$user->account->current_charges.', Previous Balance: ₱'.$user->account->prev_balance.', Total Balance: ₱'.$balance;

        $client = new Client(config('twilio.account_sid'), config('twilio.auth_token'));

        $message = $client->messages->create(
            $number,
            [
                'from' => config('twilio.from'),
                'body' => $message
            ]
        );

        return response()->json([
            'message' => 'SMS sent successfully.',
            'data' => $message
        ]);
    }
}
