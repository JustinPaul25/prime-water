<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use Twilio\Rest\Client;
use ClickSend\Api\SMSApi;
use ClickSend\Configuration;
use Illuminate\Http\Request;
use ClickSend\Api\AccountApi;
use ClickSend\Model\SmsMessage;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;
use ClickSend\Model\SmsMessageCollection;

class SMSController extends Controller
{

    public function notify(User $user)
    {
        $balance = $user->account->prev_balance + $user->account->current_charges;
        $balance = number_format($balance, 2);
        $string = $user->contact_no;
        $number = substr_replace($string, "+63", 0, 1);
        $message = 'Hello '.$user->first_name.' '.$user->last_name.' This is a notice of disconnetion from WBS. Current Bill: ₱'.$balance;

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
                $message = 'Hello '.$user->first_name.' '.$user->last_name.' This is a notice of disconnetion from WBS. Current Bill: ₱'.$balance;

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
        $date = Carbon::now()->addDays(12)->format('M-d-Y');
        $balance = $user->account->prev_balance + $user->account->current_charges;
        $balance = number_format($balance, 2);
        $string = $user->contact_no;
        $number = substr_replace($string, "+63", 0, 1);
        $message = 'Hello '.$user->first_name.' '.$user->last_name.' Here is your bill this month and the due date will be on' . $date . '. Current Bill: ₱'.$balance;

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
