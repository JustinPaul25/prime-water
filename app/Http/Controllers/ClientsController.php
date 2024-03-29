<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Address;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ClientsController extends Controller
{
    public function all()
    {
        return User::role(['Client'])->get();
    }

    public function index()
    {
        $address = Address::get();
        return Inertia::render('Client/Client', [
            'addresses' => $address
        ]);
    }

    public function list(Request $request)
    {
        $client = User::query();

        if($request->reminder === "true") {
            $client = $client->whereHas('account', function ($query) {
                $query->where('last_payment', '<', Carbon::now()->subDays(90));
            });
        }

        if($request->filled('search')) {
            $search = $request->input('search');
            $client = $client->where(function($q) use ($search){
                $q->whereRaw('lower(name) like (?)',["%{$search}%"])
                ->orWhere('username', 'LIKE', '%'.$search.'%');
            });
        }

        if ($request->filled('purok')) {
            $client = $client->where('address_id', $request->input('purok'));
        }

        if($request->filled('status')) {
            if($request->input('status') == 1) {
                $client = $client->role(['Client'])->paginate(10);
            } else {
                $client = $client->onlyTrashed()->role(['Client'])->paginate(10);
            }
        } else {
            $client = $client->withTrashed()->role(['Client'])->paginate(10);
        }

        return $client;
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|check_name_client',
            'contact_no' => 'required|string|max:255|unique:users',
            'address_id' => 'required',
            'username' => 'required|string|max:255|unique:users',
        ], [
            'name.check_name_client' => 'The name has already been taken.',
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
        $user->address_id = $request->input('address_id');
        $user->username = $request->input('username');
        $user->status = 1;
        $user->password = Hash::make('pwclient');
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
            'name' => 'required|string|max:255|check_name_client:'.$user->id,
            'contact_no' => 'required|string|max:255',
            'address_id' => 'required',
            'username' => 'required|string|unique:users,username,'.$user->id,
            'status' => 'required',
        ], [
            'name.check_name_client' => 'The name has already been taken.',
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
            'address_id' => $request->input('address_id'),
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

    public function profile($id)
    {
        $user = User::find($id);

        if(!$user) {
            $user =  User::onlyTrashed()->find($id);
        }

        return Inertia::render('Client/ClientProfile', [
            'client' => $user,
            'account' => $user->account,
        ]);
    }

    public function switchStatus($id)
    {
        $user = User::find($id);;

        if($user) {
            $user->delete();
        } else {
            $user =  User::onlyTrashed()->find($id);
            $user->restore();
        }

        return $user->status;
    }
}
