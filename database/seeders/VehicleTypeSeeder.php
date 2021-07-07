<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Seed the three types of vehicle types that are in the task
        VehicleType::create([
            'name' => 'Лек автомобил/мотор',
            'category' => 'A',
            'parking_lot_id' => 1,
            'daily_rate' => 3,
            'night_rate' => 2,
            'parking_space' => 1,
        ]);

        VehicleType::create([
            'name' => 'Бус',
            'category' => 'Б',
            'parking_lot_id' => 1,
            'daily_rate' => 6,
            'night_rate' => 4,
            'parking_space' => 2,
        ]);

        VehicleType::create([
            'name' => 'Автобус / камион',
            'category' => 'C',
            'parking_lot_id' => 1,
            'daily_rate' => 12,
            'night_rate' => 8,
            'parking_space' => 4,
        ]);
    }
}
