<?php

namespace App\Http\Controllers;


use App\Actions\Address\ListAllNeighbnorhoodsAction;
use App\Actions\Address\ListNeighbnorhoodsAction;
use App\Base\Http\Controllers\BaseController;
use App\Http\Resources\NeighborhoodResource;
use App\Http\Resources\NeighborhoodsResource;

class NeighborhoodController extends BaseController
{
    /**
     * @param string|int $city_id
     * @param ListNeighbnorhoodsAction $listNeighbnorhoodsAction
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(string|int $city_id, ListNeighbnorhoodsAction $listNeighbnorhoodsAction)
    {
        return NeighborhoodResource::collection($listNeighbnorhoodsAction->execute($city_id));
    }

    /**
     * @param ListAllNeighbnorhoodsAction $listAllNeighbnorhoodsAction
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function GetNeighborhoods(ListAllNeighbnorhoodsAction $listAllNeighbnorhoodsAction)
    {
        return NeighborhoodsResource::collection($listAllNeighbnorhoodsAction->execute());
    }
}
