<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Seed the three types of vehicle types that are in the task
        Card::create([
            'name' => 'Silver',
            'parking_lot_id' => 1,
            'discount' => 10,
        ]);

        Card::create([
            'name' => 'Gold',
            'parking_lot_id' => 1,
            'discount' => 15,
        ]);

        Card::create([
            'name' => 'Platinum',
            'parking_lot_id' => 1,
            'discount' => 20,
        ]);
    }
}
