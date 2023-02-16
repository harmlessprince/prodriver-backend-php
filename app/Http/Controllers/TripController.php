<?php

namespace App\Http\Controllers;

use App\Imports\TripsImport;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TripController extends Controller
{

    public function updateWaybillStatus(Request $request)
    {
    }

    public function updateTripStatus(Request $request, Trip $trip)
    {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $tripQuery = Trip::query()->search()->with(Trip::RELATIONS)->latest('created_at');
        if ($user->isAccountManager()) {
            $tripQuery = $tripQuery->where('account_manager_id', $user->id);
        }
        if ($user->isTransporter()) {
            $tripQuery = $tripQuery->where('transporter_id', $user->id);
        }
        if ($user->isCargoOwner()) {
            $tripQuery = $tripQuery->where('cargo_owner_id', $user->id);
        }
        return $this->respondSuccess(['trips' => $tripQuery->paginate()], 'Trips fetched successfully');
    }

    public function importTrips(Request $request)
    {
        $this->validate($request, [
            'file' => ['required', 'file']
        ]);

        $import = new TripsImport();
        $import->onlySheets('DATABASE');
        Excel::queueImport($import, $request->file('file'));

        // Excel::import(new UsersImport, 'users.xlsx');
    }
}
