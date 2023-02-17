<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use App\Models\TripStatus;
use Illuminate\Support\Str;
use App\Imports\TripsImport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TripRequest;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TripController extends Controller
{


    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $totalTripsQuery = Trip::query();
        $ripsStatusQuery = TripStatus::query();
        $tripQuery = Trip::query()->search()->with(Trip::RELATIONS)->latest('created_at');
        $filterTransporter = function ($query) use ($user) {
            $query->where('transporter_id', $user->id);
        };
        $filterCargoOwner = function ($query) use ($user) {
            $query->where('cargo_owner_id', $user->id);
        };
        $filterAccountManager = function ($query) use ($user) {
            $query->where('account_manager_id', $user->id);
        };


        if ($user->isAccountManager()) {
            $totalTripsQuery = $totalTripsQuery->where('trips.account_manager_id', $user->id);
            $tripStatusCountsQuery  =  $ripsStatusQuery->withCount(['trips' => $filterAccountManager]);
            $tripQuery = $tripQuery->where('account_manager_id', $user->id);
        } else if ($user->isTransporter()) {
            $totalTripsQuery = $totalTripsQuery->where('trips.transporter_id', $user->id);
            $tripStatusCountsQuery  =  $ripsStatusQuery->withCount(['trips' => $filterTransporter]);
            $tripQuery = $tripQuery->where('transporter_id', $user->id);
        } else if ($user->isCargoOwner()) {
            $totalTripsQuery = $totalTripsQuery->where('trips.cargo_owner_id', $user->id);
            $tripStatusCountsQuery  =  $ripsStatusQuery->withCount(['trips' => $filterCargoOwner]);
            $tripQuery = $tripQuery->where('cargo_owner_id', $user->id);
        } else {
            $tripStatusCountsQuery  =  $ripsStatusQuery->withCount('trips');
        }
        $tripStatusCounts =  $tripStatusCountsQuery->get()->map(function ($tripStatus) {
            return [
                'name' => $tripStatus->name,
                'display_name' => Str::headline($tripStatus->name),
                'count' => $tripStatus->trips_count,
            ];
        });

        return $this->respondSuccess([
            'trips' => $tripQuery->paginate(),

            'meta' => [
                'totalTrips' => $totalTripsQuery->count(),
                'tripStatusCounts' => $tripStatusCounts,
            ]


        ], 'Trips fetched successfully');
    }


    public function update(TripRequest $request, Trip $trip)
    {
        $trip->update($request->validated());
        return $this->respondSuccess(['trip' => $trip->fresh(Trip::RELATIONS)], 'Trip updated');
    }

    public function importTrips(Request $request)
    {
        $this->validate($request, [
            'file' => ['required', 'file']
        ]);

        $import = new TripsImport();
        $import->onlySheets('DATABASE');
        Excel::queueImport($import, $request->file('file'));
    }

    public function updateWaybillStatus(Request $request, Trip $trip)
    {
        $this->validate($request, [
            'status' => ['required', 'integer', 'exists:waybill_statuses,id']
        ]);

        $trip->way_bill_status_id =  $request->input('status');
        $trip->save();
        return $this->respondSuccess(['trip' => $trip->fresh(Trip::RELATIONS)], 'Trip waybill status updated');
    }



    public function updateTripStatus(Request $request, Trip $trip)
    {
        $this->validate($request, [
            'status' => ['required', 'integer', 'exists:trip_statuses,id']
        ]);
        $trip->trip_status_id =  $request->input('status');
        $trip->save();
        return $this->respondSuccess(['trip' => $trip->fresh(Trip::RELATIONS)], 'Trip status updated');
    }

    public function dumFunction()
    {
        // $tripStatusCountsQuery = Trip::query()
        // ->rightJoin('trip_statuses', 'trip_statuses.id', '=', 'trips.trip_status_id')
        // ->select('trip_statuses.id as status_id',  'trip_statuses.name as trip_status_name', DB::raw('COALESCE(COUNT(trips.id), 0) as count'))
        // ->groupBy('trip_statuses.id', 'trip_statuses.name');


        // $groupedTripStatusesId = collect($groupedTripStatuses->pluck('status_id'));
        // $allTripStatuses = TripStatus::get();
        // $ungroupedTripStatuses = $allTripStatuses->map(function ($tripStatus) use ($groupedTripStatusesId) {
        //     if (!$groupedTripStatusesId->contains($tripStatus->id)) {
        //         return [
        //             'status_id' => $tripStatus->id,
        //             'formatted_trip_status_name' => Str::headline($tripStatus->name),
        //             'count' => 0,
        //         ];
        //     }
        // })->filter();
        // $tripStatusCounts = $groupedTripStatuses->merge($ungroupedTripStatuses);
    }
}
