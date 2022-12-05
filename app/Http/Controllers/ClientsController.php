<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientsController extends Controller
{
    public function all()
    {
        return User::role(['client'])->get();
    }

    public function index()
    {
        return Inertia::render('Client/Client');
    }

    public function list(Request $request)
    {
        $client = User::query();

        if($request->filled('search')) {
            $search = $request->input('search');
            $client->where(function($q) use ($search){
                $q->where('first_name', 'LIKE', '%'.$search.'%')
                ->orWhere('middle_name', 'LIKE', '%'.$search.'%')
                ->orWhere('last_name', 'LIKE', '%'.$search.'%')
                ->orWhere('username', 'LIKE', '%'.$search.'%');
            });
        }

        if($request->filled('status')) {
            $client = $client->where('status', $request->input('status'));
        }

        $client = $client->role(['client'])->paginate(10);

        return $client;
    }

    public function create(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
        ]);

        $name = $request->input('first_name');
        if($request->filled('middle_name')) {
            $name = $name.' '.$request->input('middle_name').' '.$request->input('last_name');
        } else {
            $name = $name.' '.$request->input('last_name');
        }

        $user = new User;
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->name = $name;
        $user->contact_no = $request->input('contact_no');
        $user->address = $request->input('address');
        $user->username = $request->input('username');
        $user->status = 1;
        $user->password = Hash::make('PW-Staff');
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole('Client');

        $account = new Account;
        $account->client_id = $user->id;
        $account->prev_reading = 0;
        $account->current_reading = 0;
        $account->prev_balance = 0;
        $account->current_charges = 0;
        $account->save();

        return;
    }

    public function show(User $user)
    {
        return Inertia::render('Client/ClientProfile', [
            'client' => $user,
            'readings' => $user->reading,
            'account' => $user->account,
        ]);
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,'.$user->id,
            'status' => 'required',
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
            'address' => $request->input('address'),
            'username' => $request->input('username'),
            'status' => $request->input('status'),
        ]);

        return;
    }

    public function destroy(User $user)
    {
        $user->delete();
        sleep(1);

        return;
    }

    public function data(User $user)
    {
        return $user;
    }

    public function profile(User $user)
    {
        return Inertia::render('Client/ClientProfile', [
            'client' => $user,
            'account' => $user->account,
        ]);
    }

    public function switchStatus(User $user)
    {
        $user->update([
            'status' => !$user->status,
        ]);   

        return $user->status;
    }
}
