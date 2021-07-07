<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingLot;

class ParkingLotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ParkingLot::create([
            'address' => '1000 Център, София',
            'space' => 200,
            'timezone' => 'Europe/Sofia',
            'currency' => 'BGN'
        ]);
    }
}
