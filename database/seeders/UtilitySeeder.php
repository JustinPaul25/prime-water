<?php

namespace Database\Seeders;

use App\Models\Utility;
use Illuminate\Database\Seeder;

class UtilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $utility = new Utility;
        $utility->field = 'price';
        $utility->value = '26';
        $utility->is_active = true;
        $utility->user_id = 1;
        $utility->save();
    }
}
