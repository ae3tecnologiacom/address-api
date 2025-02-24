<?php

namespace App\Http\Controllers;

use App\Actions\Address\ListStatesAction;
use App\Base\Http\Controllers\BaseController;
use App\Http\Resources\StateResource;
use Illuminate\Http\Request;

class StateController extends BaseController
{
    /**
     * @param Request $request
     * @param ListStatesAction $listStatesAction
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(Request $request, ListStatesAction $listStatesAction)
    {
        return StateResource::collection($listStatesAction->execute());
    }
}
