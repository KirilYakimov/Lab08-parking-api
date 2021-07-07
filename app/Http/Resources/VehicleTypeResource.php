<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleTypeResource extends JsonResource
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
            'name' => $this->name,
            'category' => $this->category,
            'parking_lot_id' => $this->parking_lot_id,
            'daily_rate' => $this->daily_rate,
            'night_rate' => $this->night_rate,
            'parking_space' => $this->parking_space,
        ];
    }
}
