<?php

namespace App\Actions\Address;

use App\Models\Locations;

readonly class ListAllCitiesAction
{
    /**
     * @return Locations[]|\LaravelIdea\Helper\App\Models\_IH_Locations_C
     */
    public function execute()
    {
        return Locations::orderBy('name')->get([
            'id as id',
            'name as value',
        ]);
    }
}
