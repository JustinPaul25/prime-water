<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', Password::defaults()],
        ]);

        $user = auth()->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/');
    }

    public function changePassword()
    {
        return Inertia::render('ChangePassword');
    }
}
