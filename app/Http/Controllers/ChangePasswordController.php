<?php

namespace App\Http\Controllers;

use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function change(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users,username',
        ], [
            'username.required' => 'The username field is required.',
            'username.exists' => 'Username does not exist.',
        ]);

        $user = User::where('username', $request->username)->first();

        $randomBytes = random_bytes(6);
        $randomString = bin2hex($randomBytes);

        $message = 'Hello '.$user->first_name.' '.$user->last_name.'This is WBS your new password:'.$randomString;
        $string = $user->contact_no;
        $number = substr_replace($string, "+63", 0, 1);
        $client = new Client(config('twilio.account_sid'), config('twilio.auth_token'));

        $message = $client->messages->create(
            $number,
            [
                'from' => config('twilio.from'),
                'body' => $message
            ]
        );

        $user->update([
            'password' => Hash::make($randomString),
        ]);

        return response()->json([
            'message' => 'SMS sent successfully.',
            'data' => $message
        ]);
    }
}
