<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'part_number' => $this->part_number,
            'lot_number' => $this->lot_number,
            'quantity' => $this->quantity,
            'date_manufactured' => $this->date_manufactured,
            'serial_number' => $this->serial_number,
            'date_received' => $this->created_at,
            'date_released' => $this->date_released,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
