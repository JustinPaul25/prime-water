<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $address = [
            '1',
            '1A',
            '2',
            '3',
            '4',
        ];

        foreach($address as $add) {
            Address::create([
                'prk' => $add
            ]);
        }
    }
}
