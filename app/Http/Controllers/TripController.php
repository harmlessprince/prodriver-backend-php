<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{

    public function updateWaybillStatus(Request $request)
    {

    }

    public function updateTripStatus(Request $request)
    {

    }

    public function index(Request $request){
        $trips = Trip::query()->with(Trip::RELATIONS)->latest('created_at');
        return $this->respondSuccess(['trips' => $trips->simplePaginate()], 'Trips fetched successfully');
    }



}
