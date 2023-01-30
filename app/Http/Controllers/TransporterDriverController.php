<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripStatus;
use App\Models\User;
use Illuminate\Http\Request;

class TransporterDriverController extends Controller
{
    public function index(Request $request, User $transporter)
    {
        $inActiveTripStatuses = TripStatus::whereIn('name', Trip::INACTIVE_TRIP_STATUSES)->get()->pluck('id')->toArray();
        if ($transporter->user_type !== User::USER_TYPE_TRANSPORTER) {
            return $this->respondSuccess([], 'Invalid transporter id supplied');
        }
        $drivers = $transporter->drivers()->whereDoesntHave('trips', function ($query) use ($inActiveTripStatuses) {
            $query->whereIn('trip_status_id', $inActiveTripStatuses);
        })->get();
        return $this->respondSuccess(['drivers' => $drivers], 'Transporter drivers fetched successfully');
    }
}
