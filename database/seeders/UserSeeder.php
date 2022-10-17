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
        $user->username = 'admin';
        $user->password = Hash::make('123123');
        $user->status = true;
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole('Admin');

        $user = new User;
        $user->name = 'Meter';
        $user->username = 'meterman';
        $user->password = Hash::make('123123');
        $user->status = true;
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole('Meterman');

        $user = new User;
        $user->name = 'Cashier';
        $user->username = 'cashier';
        $user->password = Hash::make('123123');
        $user->status = true;
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole('Cashier');
    }
}
