<?php

namespace App\Actions\Address;

use App\Models\Neighborhood;

readonly class ListAllNeighbnorhoodsAction
{

    /**
     * @return Neighborhood[]|\LaravelIdea\Helper\App\Models\_IH_Neighborhood_C
     */
    public function execute()
    {

        return Neighborhood::orderBy('full_name')->get([
            'id as id',
            'full_name as value',
        ]);
    }
}
