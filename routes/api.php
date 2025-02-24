<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\ZipcodeAddressesController;

Route::prefix('v1')->group(function () {
    Route::prefix('address')->group(function () {
        Route::get('states', StateController::class);
        Route::get('cities', [CityController::class, 'GetCities']);
        Route::get('neighborhoods', [NeighborhoodController::class, 'GetNeighborhoods']);

        Route::get('states/{uf}/cities', CityController::class);
        Route::get('neighborhoods/{city_id}', NeighborhoodController::class);
        Route::get('zipcodes/addresses', ZipcodeAddressesController::class);
    });
});
