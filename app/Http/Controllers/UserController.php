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

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,'.$user->id,
        ]);

        $name = $request->input('first_name');
        if($request->filled('middle_name')) {
            $name = $name.' '.$request->input('middle_name').' '.$request->input('last_name');
        } else {
            $name = $name.' '.$request->input('last_name');
        }

        $user->update([
            'name' => $name,
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'contact_no' => $request->input('contact_no'),
            'username' => $request->input('username'),
        ]);

        return $user->name;
    }
}
