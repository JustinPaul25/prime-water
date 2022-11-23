<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $attrs = $request->validate([
            'username' => 'required',
            'password' => 'required|min:6',
        ]);

        if(!Auth::attempt($attrs)) {
            return response([
                'message' => 'Invalid Credentials.'
            ], 403);
        }

        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);
    }

    public function logout()
    {
        auth()->user()->token()->delete();
        return response ([
            'message' => 'Logout Success.',
        ], 200);
    }
}
