<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'address' => $this->full_name,
            'zipcode' => $this->zip_code,
            'neighborhood_id' => $this->neighborhood_id,
            'location' => [
                'id' => $this->city?->id,
                'city' => $this->city?->name,
                'state' => $this->city?->state?->name,
            ],
        ];
    }
}
