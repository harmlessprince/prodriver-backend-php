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
use App\Models\File;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class TripController extends Controller
{


    public function index(Request $request, AnalyticsService $analyticsService)
    {
        /** @var User $user */
        $user = $request->user();
        $tripQuery = Trip::query()->search();

        if ($user->user_type === User::USER_TYPE_TRANSPORTER) {
            $tripQuery = $tripQuery->where('transporter_id', $user->id);
        }


        if ($user->user_type === User::USER_TYPE_CARGO_OWNER) {
            $tripQuery = $tripQuery->where('cargo_owner_id', $user->id);
        }

        $tripQuery = $tripQuery->filter($request->all(), $tripQuery);

        $tripQuery = $tripQuery->with(Trip::RELATIONS)->orderBy('trip_id', 'ASC');

        return $this->respondSuccess([
            'trips' => $tripQuery->paginate(request('per_page', 15)),

            'meta' => [
                'totalNumberOfCompletedTrips' => $analyticsService->totalNumberOfCompletedTrips(clone $tripQuery, $user),
                'totalNumberOfCancelledTrips' => $analyticsService->totalNumberOfCancelledTrips(clone $tripQuery, $user),
                'totalNumberOfTrips' => $analyticsService->totalNumberOfTrips(clone $tripQuery, $user),
                'totalNumberOfOngoingTrips' => $analyticsService->totalNumberOfOngoingTrips(clone $tripQuery, $user),
                'totalNumberOfDivertedTrips' => $analyticsService->totalNumberOfDivertedTrips(clone $tripQuery, $user),
                'totalNumberOfAccidentsTrips' => $analyticsService->totalNumberOfAccidentsTrips(clone $tripQuery, $user),
            ]

        ], 'Trips fetched successfully');
    }



    public function update(TripRequest $request, Trip $trip)
    {
        $data = $request->validated();
        $incidental_cost = $request->input('incidental_cost', 0.00);
        if (in_array('incidental_cost', $data)) {
            //    unset($data['incidental_cost']);
            if ($incidental_cost > $trip->incidental_cost) {
                $costToDeduct = $incidental_cost - $trip->incidental_cost;
                $data['incidental_cost']  = $costToDeduct;
                $data['balance_payout'] = $trip->balance_payout - $costToDeduct;
                $trip->net_margin_profit_amount = $trip->margin_profit_amount - $incidental_cost;
            }
        }
        $trip->update($data);
        $trip = $trip->refresh();
        if ($trip->tripStatus->name == TripStatus::STATUS_COMPLETED) {
            $trip->truck()->update(['on_trip' => false]);
            $trip->completed_at = Carbon::now();
            $trip->save();
        }
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

    public function uploadWaybillPicture(Request $request, Trip $trip)
    {

        $user = request()->user();
        $fileExists = Rule::exists(File::class, 'id')
            ->where('type', File::TYPE_IMAGE)
            ->where('creator_id', $user->id);

        $this->validate($request, [
            'picture_id' => ['required', 'integer',  $fileExists]
        ]);

        $waybillPicture =  $trip->tripWaybillPictures()->create([
            'picture_id' => $request->input('picture_id'),
            'uploaded_by' => auth()->id(),
        ]);

        return $this->respondSuccess(['waybill_picture' =>  $waybillPicture], 'Trip waybill status updated');
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



        //filtering
        // $filterTransporter = function ($query) use ($user) {
        //     $query->where('transporter_id', $user->id);
        // };
        // $filterCargoOwner = function ($query) use ($user) {
        //     $query->where('cargo_owner_id', $user->id);
        // };
        // $filterAccountManager = function ($query) use ($user) {
        //     $query->where('account_manager_id', $user->id);
        // };


        //filtering
        // if ($user->isAccountManager()) {
        //     $totalTripsQuery = $totalTripsQuery->where('trips.account_manager_id', $user->id);
        //     $tripStatusCountsQuery  =  $ripsStatusQuery->withCount(['trips' => $filterAccountManager]);
        //     $tripQuery = $tripQuery->where('account_manager_id', $user->id);
        // } else if ($user->isTransporter()) {
        //     $totalTripsQuery = $totalTripsQuery->where('trips.transporter_id', $user->id);
        //     $tripStatusCountsQuery  =  $ripsStatusQuery->withCount(['trips' => $filterTransporter]);
        //     $tripQuery = $tripQuery->where('transporter_id', $user->id);
        // } else if ($user->isCargoOwner()) {
        //     $totalTripsQuery = $totalTripsQuery->where('trips.cargo_owner_id', $user->id);
        //     $tripStatusCountsQuery  =  $ripsStatusQuery->withCount(['trips' => $filterCargoOwner]);
        //     $tripQuery = $tripQuery->where('cargo_owner_id', $user->id);
        // } else {
        //     $tripStatusCountsQuery  =  $ripsStatusQuery->withCount('trips');
        // }
        // $tripStatusCounts =  $tripStatusCountsQuery->get()->map(function ($tripStatus) {
        //     return [
        //         'name' => $tripStatus->name,
        //         'display_name' => Str::headline($tripStatus->name),
        //         'count' => $tripStatus->trips_count,
        //     ];
        // });

    }
}
