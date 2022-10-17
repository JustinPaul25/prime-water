<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Faker\Factory;
use App\Models\User;
use App\Models\Account;
use App\Models\Reading;
use App\Models\Utility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        foreach( range(1, 100) as $index ){
            $user = new User;
            $user->name = $faker->name();
            $user->username = $faker->name();
            $user->first_name = $faker->firstName();
            $user->last_name = $faker->lastName();
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

            $price = Utility::find(1);
            $randReading = rand(10,200);

            $reading_diff = $randReading - $account->current_reading;

            $payment = $reading_diff * $price->value;

            $account->prev_reading = $account->current_reading;
            $account->current_reading = $randReading;
            $account->current_charges = $payment;
            $account->update();

            $reading = new Reading;
                $reading->client_id = $user->id;
                $reading->meterman_id = 2;
                $reading->prev_reading = $randReading - $reading_diff;
                $reading->current_reading = $randReading;
                $reading->price = $payment;
                $reading->save();
            }
    }
}
