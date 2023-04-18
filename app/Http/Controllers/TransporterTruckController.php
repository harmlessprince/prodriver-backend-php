<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use App\Models\User;
use Illuminate\Http\Request;

class TransporterTruckController extends Controller
{
    public function index(Request $request, $transporter_id)
    {
        $trucks =  Truck::query()->where('transporter_id', $transporter_id)
        ->where('on_trip', false)
        ->with(Truck::NON_DOCUMENT_RELATIONS)
        ->orderBy('plate_number', 'ASC')
        ->get();
        return $this->respondSuccess(['trucks' => $trucks], 'All trucks fetched successfully');
    }
}
