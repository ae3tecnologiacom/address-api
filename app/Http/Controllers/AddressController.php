<?php

namespace App\Http\Controllers;

use App\Base\Http\Controllers\BaseController;
use App\Models\Countries;
use App\Services\Address\Contracts\GetAddressByZipCodeServiceContract;
use PHPUnit\Framework\Constraint\Count;

class AddressController extends BaseController
{
    public function getCountries()
    {
        return Countries::all();
    }

    public function getAddressByZipCode(string $zip_code, GetAddressByZipCodeServiceContract $getAddressByZipCodeService)
    {
        try {
            return self::successResponse(
                data: $getAddressByZipCodeService->execute($zip_code)
            );
        } catch (\Exception $exception) {
            return self::returnError($exception);
        }

    }
}
