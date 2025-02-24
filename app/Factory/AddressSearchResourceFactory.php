<?php

namespace App\Factory;

use App\Http\Resources\NeighborhoodResource;
use App\Http\Resources\SearchAddressResource;

class AddressSearchResourceFactory
{
    public function make(?string $scope)
    {
        return match ($scope) {
            'neighborhood' => NeighborhoodResource::class,
            default => SearchAddressResource::class
        };
    }
}
