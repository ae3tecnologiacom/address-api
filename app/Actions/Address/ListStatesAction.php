<?php

namespace App\Actions\Address;


use App\Models\States;

readonly class ListStatesAction
{

    /**
     * @return States[]|\LaravelIdea\Helper\App\Models\_IH_States_C
     */
    public function execute()
    {
        return States::orderBy('acronym')->get([
            'acronym',
            'name as value'
        ]);
    }
}
