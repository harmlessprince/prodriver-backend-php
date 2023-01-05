<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{
   public function index (): JsonResponse
   {
        return $this->respondSuccess(['countries' => Country::all()], 'All countries fetched successfully');
    }

    public function show (Country $country): JsonResponse
    {
        return $this->respondSuccess(['country' => $country->load('states')], 'All countries fetched successfully');
    }
}
