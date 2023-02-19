<?php

namespace App\Services;

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
        $tripBuilder->where('flagged', 1)->count();
    }

    public function totalNumberOfTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        $tripBuilder->count();
    }

    public function totalNumberOfActiveTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {

    }

    public function totalNumberOfDeliveredTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        $tripBuilder->where('delivery_date', '!=', null)->count();
    }

    public function totalAmountOfIncome()
    {
    }

    public function totalAmountOfPendingIncome()
    {
    }

    public function totalAmountPaidAdvance()
    {
    }

    public function totalAmountReceivableAdvance()
    {
    }
}
