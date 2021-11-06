<?php

namespace App\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CountryController extends Controller
{
    public function country()
    {
        $countries = Country::get(['id', 'sortname', 'name', 'phonecode']);
    
        return response($countries, Response::HTTP_ACCEPTED);
    }

    public function state($id)
    {
        $states = State::where('country_id', $id)->get(['id', 'country_id', 'name']);
        
        return response($states, Response::HTTP_ACCEPTED);
    }
}
