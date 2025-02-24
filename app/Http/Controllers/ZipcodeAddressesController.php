<?php

namespace App\Http\Controllers;

use App\Actions\Address\SearchAddressAction;
use App\Base\Http\Controllers\BaseController;
use App\Factory\AddressSearchResourceFactory;
use Illuminate\Http\Request;

class ZipcodeAddressesController extends BaseController
{
    /**
     * @param AddressSearchResourceFactory $addressSearchResourceFactory
     */
    public function __construct(
        private readonly AddressSearchResourceFactory $addressSearchResourceFactory
    )
    {
    }

    /**
     * @param Request $request
     * @param SearchAddressAction $searchAddressAction
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Exception
     */
    public function __invoke(Request $request, SearchAddressAction $searchAddressAction)
    {
        $resource = $this->addressSearchResourceFactory->make($request->get('scope'));
        return $resource::collection($searchAddressAction->execute($request));
    }
}
