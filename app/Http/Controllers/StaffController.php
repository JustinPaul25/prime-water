<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        return Inertia::render('Staff');
    }

    public function list(Request $request)
    {
        $staff = User::query();

        if($request->filled('search')) {
            $search = $request->input('search');
            $staff->where(function($q) use ($search){
                $q->where('name', 'LIKE', '%'.$search.'%')
                ->orWhere('username', 'LIKE', '%'.$search.'%');
            });
        }

        if($request->filled('role')) {
            $staff = $staff->where('id', '!=', auth()->id())->role([$request->input('role')])->paginate(10);
        } else {
            $staff = $staff->where('id', '!=', auth()->id())->role(['Meterman', 'Cashier', 'Admin'])->paginate(10);
        }

        return $staff;
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|check_name_staff',
            'contact_no' => 'required|string|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'role' => 'required',
        ], [
            'name.check_name_staff' => 'The name has already been taken.',
        ]);

        if($request->role === 'Admin') {
            $old_admin = User::role(['Admin'])->first();
            AdminLog::create([
                'user_id' => $old_admin->id,
                'message' => $old_admin->name.' is no longer an admin and his/her account will be deleted.'
            ]);
            $old_admin->delete();
        }

        $user = new User;
        $user->name = $request->input('name');
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->username = $request->input('username');
        $user->contact_no = $request->input('contact_no');
        $user->address = $request->input('address');
        $user->status = true;
        $user->password = Hash::make('PW-Staff');
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole($request->input('role'));

        AdminLog::create([
            'user_id' => $user->id,
            'message' => "New app admin assigned to ".$user->name.".",
        ]);

        return;
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username,'.$user->id,
            'name' => 'required|string|max:255|check_name_staff:'.$user->id,
            'contact_no' => 'required|string|max:255|unique:users,contact_no,'.$user->id,
            'role' => 'required',
        ], [
            'name.check_name_staff' => 'The name has already been taken.',
        ]);

        $user->update([
            'name' => $request->input('name'),
            'contact_no' => $request->input('contact_no'),
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'username' => $request->input('username'),
            'address' => $request->input('address'),

        ]);

        $user->removeRole('Cashier');
        $user->removeRole('Meterman');

        $user->assignRole($request->input('role'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        sleep(1);

        return;
    }
}
