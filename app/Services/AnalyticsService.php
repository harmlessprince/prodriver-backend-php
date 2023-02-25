<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\TripStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class AnalyticsService
{

    public function totalNumberOfTrucks(EloquentBuilder | QueryBuilder $truckBuilder, User $user)
    {
        if ($user->isAccountManager()) {
            return 0;
        } else if ($user->isTransporter()) {
            return $truckBuilder->where('transporter_id', $user->id)->count();
        } else if ($user->isCargoOwner()) {
            return 0;
        } else if ($user->isAdmin()) {
            return $truckBuilder->count();
        } else {
            return 0;
        }
    }


    public function totalNumberOfDrivers(EloquentBuilder | QueryBuilder $driverBuilder)
    {
        return $driverBuilder->count();
    }

    public function totalNumberOfFlaggedTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return  $tripBuilder->where('flagged', 1)->count();
    }

    public function totalNumberOfTrips(EloquentBuilder | QueryBuilder $tripBuilder, User $user)
    {
        if ($user->isAccountManager()) {
            return $tripBuilder->where('account_manager_id', $user->id)->count();
        } else if ($user->isTransporter()) {
            return $tripBuilder->where('transporter_id', $user->id)->count();
        } else if ($user->isCargoOwner()) {
            return $tripBuilder->where('cargo_owner_id', $user->id)->count();
        } else if ($user->isAdmin()) {
            return $tripBuilder->count();
        } else {
            return 0;
        }
    }




    public function totalAmountOfIncome(EloquentBuilder | QueryBuilder $tripBuilder, User $user)
    {
        if ($user->user_type == User::USER_TYPE_ADMIN) {
            return $tripBuilder->sum('total_gtv');
        }

        if ($user->user_type == User::USER_TYPE_TRANSPORTER) {
            return $tripBuilder->sum('total_payout');
        }
        return 0;
    }


    public function totalNetMarginProfit(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return $tripBuilder->sum('net_margin_profit_amount');
    }

    public function totalMarginProfit(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return $tripBuilder->sum('margin_profit_amount');
    }

    public function totalIncomeForTheMonth(EloquentBuilder | QueryBuilder $tripBuilder, $user)
    {
        if ($user->user_type == User::USER_TYPE_ADMIN) {
            return  $tripBuilder->whereBetween('loading_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total_gtv');
        }

        if ($user->user_type == User::USER_TYPE_TRANSPORTER) {
            return  $tripBuilder->whereBetween('loading_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total_payout');
        }
        return 0;
    }

    public function totalAmountOfPendingIncome(EloquentBuilder | QueryBuilder $tripBuilder, $user)
    {
        if ($user->user_type == User::USER_TYPE_ADMIN) {
            return  $tripBuilder->sum('total_gtv') -  $tripBuilder->sum('advance_gtv');
        }
        if ($user->user_type == User::USER_TYPE_TRANSPORTER) {
            return  $tripBuilder->sum('total_payout') -  $tripBuilder->sum('advance_payout');
        }
        return 0;
    }


    public function totalPayout(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return  $tripBuilder->sum('total_payout');
    }

    public function totalLoadingTonnage(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return  $tripBuilder->sum('loading_tonnage_value');
    }



    public function totalNumberOfCancelledTrips(EloquentBuilder | QueryBuilder $tripBuilder, $user)
    {

        $status = TripStatus::where('name', TripStatus::STATUS_CANCELED)->first();
        $tripBuilder =   $tripBuilder->where('trip_status_id', $status->id);
        if ($user->isAccountManager()) {
            return $tripBuilder->where('account_manager_id', $user->id)->count();
        } else if ($user->isTransporter()) {
            return $tripBuilder->where('transporter_id', $user->id)->count();
        } else if ($user->isCargoOwner()) {
            return $tripBuilder->where('cargo_owner_id', $user->id)->count();
        } else if ($user->isAdmin()) {
            return $tripBuilder->count();
        } else {
            return 0;
        }
    }


    public function totalNumberOfOngoingTrips(EloquentBuilder | QueryBuilder $tripBuilder, $user)
    {
        $status = TripStatus::query()->select('id')->whereIn('name', [TripStatus::STATUS_COMPLETED, TripStatus::STATUS_CANCELED, TripStatus::STATUS_DIVERTED])->get();
        $tripBuilder =   $tripBuilder->whereNotIn('trip_status_id', $status->pluck('id')->toArray());
        if ($user->isAccountManager()) {
            return $tripBuilder->where('account_manager_id', $user->id)->count();
        } else if ($user->isTransporter()) {
            return $tripBuilder->where('transporter_id', $user->id)->count();
        } else if ($user->isCargoOwner()) {
            return $tripBuilder->where('cargo_owner_id', $user->id)->count();
        } else if ($user->isAdmin()) {
            return $tripBuilder->count();
        } else {
            return 0;
        }

        return 0;
    }

    public function totalNumberOfCompletedTrips(EloquentBuilder | QueryBuilder $tripBuilder, $user)
    {
        $status = TripStatus::where('name', TripStatus::STATUS_COMPLETED)->first();
        $tripBuilder =   $tripBuilder->where('trip_status_id', $status->id);
        if ($user->isAccountManager()) {
            return $tripBuilder->where('account_manager_id', $user->id)->count();
        } else if ($user->isTransporter()) {
            return $tripBuilder->where('transporter_id', $user->id)->count();
        } else if ($user->isCargoOwner()) {
            return $tripBuilder->where('cargo_owner_id', $user->id)->count();
        } else if ($user->isAdmin()) {
            return $tripBuilder->count();
        } else {
            return 0;
        }
    }
    public function totalNumberOfDeliveredTrips(EloquentBuilder | QueryBuilder $tripBuilder)
    {
        return  $tripBuilder->where('delivery_date', '!=', null)->count();
    }
}
