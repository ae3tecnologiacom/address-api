<?php

namespace App\Actions\Address;

use App\Models\Addresses;
use App\Models\Neighborhood;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Symfony\Component\Routing\Exception\InvalidParameterException;

readonly class SearchAddressAction
{
    /**
     * @const string
     */
    const QUERY_PARAM = 'q';

    /**
     * @const string
     */
    const QUERY_SCOPE = 'scope';

    /**
     * @param Request $request
     * @return Neighborhood[]|\LaravelIdea\Helper\App\Models\_IH_Neighborhood_C
     * @throws \Exception
     */
    public function execute(Request $request)
    {
        try {
            $query = collect($request->query);

            if (!$query->has(self::QUERY_PARAM)) {
                throw new InvalidParameterException(
                    message: __('messages.errors.required_param_not_found', ['param' => self::QUERY_PARAM])
                );
            }

            $queryAsString = $query->get(self::QUERY_PARAM);

            if ($query->has(self::QUERY_SCOPE) && $query->get(self::QUERY_SCOPE) == 'zipcode') {
                $queryAsString = onlyNumbers($queryAsString);
            }
            if($query->has(self::QUERY_SCOPE) && $query->get(self::QUERY_SCOPE) == 'neighborhood'){
                return Neighborhood::byName($queryAsString)->get();
            }

            $queryAsArray = explode(" ", $queryAsString);
            $andPart = '';
            $orPart = '';

            foreach ($queryAsArray as $key => $item) {
                $andPart .= $key > 0 ? " & $item" : "$item";
                $orPart .= $key > 0 ? " | $item" : "$item";
            }

            $address = Addresses::search($andPart, $orPart);
            if (!$address) {
                throw new ModelNotFoundException(__('messages.address.not_found'));
            }

            return $address;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
