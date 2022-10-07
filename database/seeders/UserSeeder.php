<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->name = 'Admin';
        $user->email = 'admin@email.com';
        $user->password = Hash::make('123123');
        $user->status = true;
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole('Admin');

        $user = new User;
        $user->name = 'Meter';
        $user->email = 'meterman@email.com';
        $user->password = Hash::make('123123');
        $user->status = true;
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole('Meterman');

        $user = new User;
        $user->name = 'Cashier';
        $user->email = 'cashier@email.com';
        $user->password = Hash::make('123123');
        $user->status = true;
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole('Cashier');

        $user = new User;
        $user->name = 'Client';
        $user->email = 'client@email.com';
        $user->password = Hash::make('123123');
        $user->status = true;
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
    }
}
