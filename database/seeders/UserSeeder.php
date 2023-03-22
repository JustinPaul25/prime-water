<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use App\Models\AdminLog;
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

        AdminLog::create([
            'user_id' => $user->id,
            'message' => "New app admin assigned to ".$user->name.".",
        ]);
    }
}
