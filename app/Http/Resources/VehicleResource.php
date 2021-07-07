<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'registration_plate' => $this->registration_plate,
            'parking_lot_id' => $this->parking_lot_id,
            'vehicle_type' => new VehicleTypeResource($this->vehicleType),
            'card' => new CardResource($this->card),
            'entered_at' => $this->entered_at,
            'exited_at' => $this->exited_at,
            'price_of_exit' => $this->price_of_exit,
            'in_parking' => $this->in_parking,
        ];
    }
}
