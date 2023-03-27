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

        if($user->isClient()) {
            $request->validate([
                'name' => 'required|string|max:255|check_name_client:'.$user->id,
                'contact_no' => 'required|string|max:255',
                'username' => 'required|string|unique:users,username,'.$user->id,
            ], [
                'name.check_name_client' => 'The name has already been taken.',
            ]);
        } else {
            $request->validate([
                'username' => 'required|string|unique:users,username,'.$user->id,
                'name' => 'required|string|max:255|check_name_staff:'.$user->id,
                'contact_no' => 'required|string|max:255|unique:users,contact_no,'.$user->id,
            ], [
                'name.check_name_staff' => 'The name has already been taken.',
            ]);
        }

        $user->update([
            'name' => $request->input('name'),
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'contact_no' => $request->input('contact_no'),
            'username' => $request->input('username'),
        ]);

        return $user->name;
    }
}
