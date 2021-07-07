<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParkingLotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //TODO add the card and vehicle types
        return [
            'id' => $this->id,
            'address' => $this->address,
            'space' => $this->space,
            'currency' => $this->currency,
            'timezone' => $this->timezone,
            'cards' => CardResource::collection($this->cards),
            'vehicle types' => VehicleTypeResource::collection($this->vehicleTypes)
        ];
    }
}
