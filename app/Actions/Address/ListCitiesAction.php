<?php

namespace App\Actions\Address;

use App\Models\Locations;

readonly class ListCitiesAction
{
    /**
     * @param string $uf
     * @return Locations[]|\LaravelIdea\Helper\App\Models\_IH_Locations_C
     */
    public function execute(string $uf)
    {
        return Locations::byUf($uf)
            ->sortBy()
            ->get([
                'id as id',
                'name as value',
            ]);
    }
}
