<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Reading;
use App\Models\Utility;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientsJson = File::get("database/datas/initial.json");
        $clients = json_decode($clientsJson);

        foreach($clients as $client) {
            $user = User::where('username', $client->username)->first();

            if($user) {
                continue;
            }

            $user = new User;
            $user->username = $client->username;
            $user->first_name = $client->first_name;
            $user->middle_name = $client->middle_name;
            $user->last_name = $client->last_name;
            $user->name = $client->name;
            $user->password = Hash::make($client->password);
            $user->deleted_at = $client->status ? null : Carbon::now();
            $user->status = $client->status;
            $user->email_verified_at = Carbon::now();
            $user->save();

            $user->assignRole('Client');

            $price = Utility::find(1);

            $reading_diff = (int)$client->current_reading - (int)$client->previous_reading;

            $payment = $reading_diff * $price->value;

            $account = new Account;
            $account->client_id = $user->id;
            $account->prev_reading = $client->previous_reading;
            $account->current_reading = $client->current_reading;
            $account->prev_balance = 0;
            $account->current_charges = $client->previous_reading;
            $account->last_payment = null;
            $account->current_charges = $payment;
            $account->save();

            $reading = new Reading;
            $reading->client_id = $user->id;
            $reading->meterman_id = 2;
            $reading->prev_reading = $client->previous_reading;
            $reading->current_reading = $client->current_reading;
            $reading->cum_price = $price->value;
            $reading->price = $payment;
            $reading->updated_at = '2023-02-01 10:00:00';
            $reading->created_at = '2023-02-01 10:00:00';
            $reading->save(['timestamps' => false]);
        }

        // $faker = Factory::create();

        // $date = [
        //     '2022-11-01 10:00:00',
        //     '2022-12-01 10:00:00',
        //     '2023-01-01 10:00:00',
        //     '2023-02-01 10:00:00',
        // ];

        // $datePaid = [
        //     '2022-11-02 10:00:00',
        //     '2022-12-02 10:00:00',
        //     '2023-01-02 10:00:00',
        //     '2023-02-02 10:00:00',
        // ];

        // foreach( range(1, 20) as $index ){
        //     $dateInd = rand(1,4);

        //     $addIndex = rand(1, 5);

        //     $user = new User;
        //     $user->username = $faker->name();
        //     $user->first_name = $faker->firstName();
        //     $user->last_name = $faker->lastName();
        //     $user->name = $user->first_name . ' ' . $user->last_name;
        //     $user->password = Hash::make('123123');
        //     $user->address_id = $addIndex;
        //     $user->status = true;
        //     $user->email_verified_at = Carbon::now();
        //     $user->save();

        //     $user->assignRole('Client');

        //     $account = new Account;
        //     $account->client_id = $user->id;
        //     $account->prev_reading = 0;
        //     $account->current_reading = 0;
        //     $account->prev_balance = 0;
        //     $account->current_charges = 0;
        //     $account->last_payment = '2022-07-14 11:23:04';
        //     $account->save();

        //     $price = Utility::find(1);
        //     $randReading = rand(100,200);

        //     $reading_diff = $randReading - $account->current_reading;

        //     $payment = $reading_diff * $price->value;

        //     $account->prev_reading = $account->current_reading;
        //     $account->current_reading = $randReading;
        //     $account->current_charges = $payment;
        //     $account->update();

        //     $reading = new Reading;
        //     $reading->client_id = $user->id;
        //     $reading->meterman_id = 2;
        //     $reading->prev_reading = $randReading - $reading_diff;
        //     $reading->current_reading = $randReading;
        //     $reading->cum_price = $price->value;
        //     $reading->price = $payment;
        //     $reading->updated_at = $date[$dateInd-1];
        //     $reading->created_at = $date[$dateInd-1];
        //     $reading->save(['timestamps' => false]);

        //     $newPayment = $payment - 50;

        //     if($newPayment > $account->prev_balance) {
        //         $remainingAmount = $newPayment - $account->prev_balance;
        //         $newPrev = 0;
        //         $newCur = $account->current_charges - $remainingAmount;
        //     } else {
        //         $newPrev = $account->prev_balance - $newPayment;
        //         $newCur = $account->current_charges;
        //     }

        //     $account->update([
        //         'last_payment' => $datePaid[$dateInd-1],
        //         'current_charges' => $newCur,
        //         'prev_balance' => $newPrev,
        //         'created_at' => $datePaid[$dateInd-1],
        //         'updated_at' => $datePaid[$dateInd-1],
        //     ]);

        //     Transaction::create([
        //         'client_id' => $user->id,
        //         'cashier_id' => 1,
        //         'amount' => $newPayment,
        //         'created_at' => $datePaid[$dateInd-1],
        //         'updated_at' => $datePaid[$dateInd-1],
        //     ]);
        // }

        // $dateInd = rand(1,4);

        // $addIndex = rand(1, 5);

        // $user = new User;
        // $user->username = $faker->name();
        // $user->first_name = $faker->firstName();
        // $user->last_name = $faker->lastName();
        // $user->name = $user->first_name . ' ' . $user->last_name;
        // $user->password = Hash::make('123123');
        // $user->address_id = $addIndex;
        // $user->status = true;
        // $user->email_verified_at = Carbon::now();
        // $user->save();

        // $user->assignRole('Client');

        // $account = new Account;
        // $account->client_id = $user->id;
        // $account->prev_reading = 0;
        // $account->current_reading = 0;
        // $account->prev_balance = 0;
        // $account->current_charges = 0;
        // $account->last_payment = '2022-07-14 11:23:04';
        // $account->save();

        // $price = Utility::find(1);
        // $randReading = rand(10,200);

        // $reading_diff = $randReading - $account->current_reading;

        // $payment = $reading_diff * $price->value;

        // $account->prev_reading = $account->current_reading;
        // $account->current_reading = $randReading;
        // $account->current_charges = $payment;
        // $account->update();

        // $reading = new Reading;
        // $reading->client_id = $user->id;
        // $reading->meterman_id = 2;
        // $reading->prev_reading = $randReading - $reading_diff;
        // $reading->current_reading = $randReading;
        // $reading->cum_price = $price->value;
        // $reading->price = $payment;
        // $reading->updated_at = $date[1];
        // $reading->created_at = $date[1];
        // $reading->save(['timestamps' => false]);
    }
}
