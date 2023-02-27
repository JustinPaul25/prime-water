<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Account;
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
        $username = 'mher';
        $password = 'Qwerty123';
        $number = $user->contact_no;
        $message = 'Hello '.$user->first_name.' '.$user->last_name.' This is a notice of disconnetion from WBS. Current Bill: ₱'.$user->account->curent_charges.'.00';

        $client = new Client();
        $url = 'https://api.itexmo.com/api/query?Email=elmer.angcla@dnsc.edu.ph&Password=Qwerty123';

        $data = [
            "Recipients" => $user->contact_no,
            "Message" => $message,
            "ApiCode" => "PR-ELMER566617_LK5KT",
            "SenderId" => "WBS"
        ];

        $response = $client->request('POST', $url, $data);

        $result = $response->getBody()->getContents();
        return $response;

        if ($result == '0') {
            return 'Message sent successfully!';
        } else {
            return 'Message sending failed.';
        }
    }

    public function bill(User $user)
    {
        $config = Configuration::getDefaultConfiguration()
            ->setUsername('regineroces')
            ->setPassword('RegineRoces@27');

        $apiInstance = new SMSApi(
            new GuzzleClient(),
            $config
        );

        $date = Carbon::now()->addDays(12)->format('M-d-Y');

        $message = 'Hello '.$user->first_name.' '.$user->last_name.' Here is your bill this month and the due date will be on' . $date . '. Current Bill: ₱'.$user->account->current_charges.'.00';

        $msg = new SmsMessage();
        $msg->setBody($message);
        $msg->setTo($user->contact_no);
        $msg->setSource('sdk');

        $sms_message = new SmsMessageCollection();
        $sms_message->setMessages([$msg]);

        try {
            $result = $apiInstance->smsSendPost($sms_message);
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    // public function notify(User $user)
    // {
    //     $message = 'Hello '.$user->first_name.' '.$user->last_name.' This is a notice of disconnetion from WBS. Current Bill: '.$user->account->curent_charges.'.';

    //     // $basic  = new \Vonage\Client\Credentials\Basic("91feaea9", "VoyeRquAsliUvY6V");
    //     // $client = new \Vonage\Client($basic);

    //     // $response = $client->sms()->send(
    //     //     new \Vonage\SMS\Message\SMS("639293194425", 'NEXMO', $message)
    //     // );

    //     $response = Http::get('http://MingSms.mingming13.repl.co?phone=' . '09934451453' . '&message=' . $message . '&key=wbs_system');
    //     //$send = json_encode(file_get_contents('https://MingSms.mingming13.repl.co?phone=' . '09934451453' . '&message=' . $message . '&key=wbs_system'));

    //     return $response;
    // }
}
