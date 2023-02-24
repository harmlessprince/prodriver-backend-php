<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\TripStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class AnalyticsService
{

    public function totalNumberOfTrucks(EloquentBuilder | QueryBuilder $truckBuilder)
    {
        return $truckBuilder->count();
    }


    public function totalNumberOfDrivers(EloquentBuilder | QueryBuilder $driverBuilder)
    {
        return $driverBuilder->count();
    }

    public function totalNumberOfFlaggedTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return  $tripBuilder->where('flagged', 1)->count();
    }

    public function totalNumberOfTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return  $tripBuilder->count();
    }

  
    public function totalNumberOfDeliveredTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return  $tripBuilder->where('delivery_date', '!=', null)->count();
    }

    public function totalAmountOfIncome(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return $tripBuilder->sum('total_gtv');
    }

    public function totalIncomeForTheMonth(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return  $tripBuilder->whereBetween('loading_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total_gtv');
    }

    public function totalAmountOfPendingIncome(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return  $tripBuilder->sum('total_gtv') -  $tripBuilder->sum('advance_gtv');
    }

    public function totalPayout(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return  $tripBuilder->sum('total_payout');
    }



    public function totalNumberOfCancelledTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        $status = TripStatus::where('name', TripStatus::STATUS_CANCELED)->first();
        return $tripBuilder->where('trip_status_id', $status->id)->count();
    }

    public function totalNumberOfCompletedTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        $status = TripStatus::where('name', TripStatus::STATUS_COMPLETED)->first();
        return $tripBuilder->where('trip_status_id', $status->id)->count();
    }

    public function totalNumberOfOngoingTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        $status = TripStatus::query()->select('id')->whereIn('name', [TripStatus::STATUS_COMPLETED, TripStatus::STATUS_CANCELED, TripStatus::STATUS_DIVERTED])->get();

        return $tripBuilder->whereNotIn('trip_status_id', $status->pluck('id')->toArray())->count();
    }
}
