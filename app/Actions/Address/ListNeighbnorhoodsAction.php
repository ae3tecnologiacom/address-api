<?php

namespace App\Actions\Address;

use App\Models\Neighborhood;

readonly class ListNeighbnorhoodsAction
{

    /**
     * @param string|int $city_id
     * @return mixed
     */
    public function execute(string|int $city_id)
    {

        return Neighborhood::byCity($city_id)
            ->sortBy('full_name')
            ->get();
    }
}
