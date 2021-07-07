<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Card;

class ParkingLot extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'address',
        'space',
        'timezone',
        'currency',
    ];

    protected $cascadeDeletes = ['cards', 'vehicleTypes', 'vehicles'];

    protected $dates = ['deleted_at'];

    public function remainingLotSpaces(){
        return $this->space - $this->lotSpacesTaken();
    }

    public function lotSpacesTaken(){
       return DB::table('vehicles')
        ->join('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id')
        ->join('parking_lots', 'vehicles.parking_lot_id', '=', 'parking_lots.id')
        ->where('vehicles.parking_lot_id', '=', $this->id)
        ->where('vehicles.in_parking', '=', true)
        ->sum('vehicle_types.parking_space');
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
    
    public function vehicleTypes()
    {
        return $this->hasMany(VehicleType::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
