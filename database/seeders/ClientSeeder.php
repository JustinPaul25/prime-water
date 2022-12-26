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

        $address = [
            'Prk - 1 Consolacion Panabo City Davao Del Norte',
            'Prk - 1A Consolacion Panabo City Davao Del Norte',
            'Prk - 2 Consolacion Panabo City Davao Del Norte',
            'Prk - 3 Consolacion Panabo City Davao Del Norte',
            'Prk - 4 Consolacion Panabo City Davao Del Norte',
        ];

        $date = [
            '2022-10-01 10:00:00',
            '2022-11-01 10:00:00',
            '2022-12-01 10:00:00',
        ];

        foreach( range(1, 100) as $index ){
            $dateInd = rand(1,3);

            $addIndex = rand(0, 4);

            $user = new User;
            $user->name = $faker->name();
            $user->username = $faker->name();
            $user->first_name = $faker->firstName();
            $user->last_name = $faker->lastName();
            $user->password = Hash::make('123123');
            $user->address = $address[$addIndex];
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
            $account->last_payment = '2022-07-14 11:23:04';
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
            $reading->cum_price = $price->value;
            $reading->price = $payment;
            $reading->updated_at = $date[$dateInd-1];
            $reading->created_at = $date[$dateInd-1];
            $reading->save(['timestamps' => false]);
        }
    }
}
