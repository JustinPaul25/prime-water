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
        $config = Configuration::getDefaultConfiguration()
            ->setUsername('elmer.angcla@dnsc.edu.ph')
            ->setPassword('1B05DB49-A364-907D-AB74-56AC71BC7476');

        $apiInstance = new SMSApi(
            new GuzzleClient(),
            $config
        );

        $message = 'Hello '.$user->first_name.' '.$user->last_name.' This is a notice of disconnetion from WBS. Current Bill: '.$user->account->curent_charges.'.';

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

    public function bill(User $user)
    {
        $config = Configuration::getDefaultConfiguration()
            ->setUsername('elmer.angcla@dnsc.edu.ph')
            ->setPassword('1B05DB49-A364-907D-AB74-56AC71BC7476');

        $apiInstance = new SMSApi(
            new GuzzleClient(),
            $config
        );

        $date = Carbon::now()->addDays(12)->format('M-d-Y');

        $message = 'Hello '.$user->first_name.' '.$user->last_name.' Here is your bill this month and the due date will be on' . $date . '. Current Bill: '.$user->account->current_charges.'.';

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
