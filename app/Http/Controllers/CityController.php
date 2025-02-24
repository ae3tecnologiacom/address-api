<?php

namespace App\Http\Controllers;


use App\Actions\Address\ListAllCitiesAction;
use App\Actions\Address\ListCitiesAction;
use App\Base\Http\Controllers\BaseController;
use App\Http\Resources\CityResource;
use App\Http\Resources\StateResource;

class CityController extends BaseController
{
    /**
     * @param string $uf
     * @param ListCitiesAction $listCitiesAction
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(string $uf, ListCitiesAction $listCitiesAction)
    {
        return CityResource::collection($listCitiesAction->execute($uf));
    }

    /**
     * @param ListAllCitiesAction $listAllCitiesAction
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function GetCities(ListAllCitiesAction $listAllCitiesAction)
    {
        return CityResource::collection($listAllCitiesAction->execute());
    }
}
