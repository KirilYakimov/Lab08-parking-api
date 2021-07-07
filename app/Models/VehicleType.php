<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleType extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'category',
        'parking_lot_id',
        'daily_rate',
        'night_rate',
        'parking_space',
    ];

    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class);
    }

    /**
     * Get the vehicles.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
