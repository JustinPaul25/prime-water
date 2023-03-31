<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Address;
use App\Rules\CiUnique;
use Illuminate\Http\Request;


class AddressController extends Controller
{
    public function index()
    {
        return Inertia::render('Address');
    }

    public function list(Request $request)
    {
        $address = Address::query();

        if($request->filled('search')) {
            $search = $request->input('search');
            $address = $address->where(function($q) use ($search){
                $q->Where('prk', 'LIKE', '%'.$search.'%');
            });
        }
        return $address->paginate(10);
    }

    public function create(Request $request)
    {
        $request->validate([
            'prk' => ['required', 'string', new CiUnique('addresses', 'prk')],
        ]);

        Address::create([
            'prk' => $request->prk
        ]);

        return;
    }

    public function update(Request $request, Address $address)
    {
        $request->validate([
            'prk' => ['required', 'string', new CiUnique('addresses', 'prk', $address->id)],
        ]);

        $address->update([
            'prk' => $request->prk
        ]);

        return;
    }

    public function destroy(Address $address)
    {
        User::where('address_id', $address->id)->update(['address_id' => null]);

        $address->delete();
        return;
    }
}
