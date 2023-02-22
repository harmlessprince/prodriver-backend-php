<?php

namespace App\DataTransferObjects;

use App\Http\Requests\TripRequest;
use App\Http\Requests\TruckRequest;
use App\Models\Trip;
use Carbon\Carbon;

class TripDto
{

    public  Carbon|null $loading_date;
    public  Carbon|null $delivery_date;
    public  Carbon|null $completed_date;
    public function __construct(
        public readonly string    $trip_id,
        public readonly int    $approved_by,
        public readonly int|null    $matched_by,
        public readonly int|null    $declined_by,
        public readonly int|null    $account_manager_id,
        public readonly int    $accepted_order_id,
        public readonly int    $driver_id,
        public readonly int    $truck_id,
        public readonly int    $order_id,
        public readonly int    $cargo_owner_id,
        public readonly int    $transporter_id,
        public readonly int|null    $way_bill_picture_id,
        public readonly float|null  $total_payout,
        public readonly float|null  $advance_payout,
        public readonly float|null  $balance_payout,
        public readonly float|null  $incidental_cost,
        public readonly float|null  $net_margin_profit_amount,
        public readonly float|null  $margin_profit_amount,
        public readonly float|null  $margin_profit_percentage,
        public readonly string    $payout_status,
        public readonly string    $delivery_status,
        public readonly float|null  $loading_tonnage_value,
        public readonly float|null  $offloading_tonnage_value,
        public readonly int|null  $days_in_transit,
        public readonly int|null  $days_delivered,
        public readonly bool|null  $flagged,
        public readonly int|null  $flagged_by,
        public readonly ?int    $trip_status_id,
        public readonly ?int    $way_bill_status_id,
    ) {
        $this->loading_date =  null;
        $this->delivery_date = null;
        $this->completed_date = null;
    }


    public static function fromModel(Trip $trip)
    {
        $object =  new self(
            $trip->trip_id,
            $trip->approved_by,
            $trip->matched_by,
            $trip->declined_by,
            $trip->account_manager_id,
            $trip->accepted_order_id,
            $trip->driver_id,
            $trip->truck_id,
            $trip->order_id,
            $trip->cargo_owner_id,
            $trip->transporter_id,
            $trip->way_bill_picture_id,
            $trip->total_payout,
            $trip->advance_payout,
            $trip->balance_payout,
            $trip->incidental_cost,
            $trip->net_margin_profit_amount,
            $trip->margin_profit_amount,
            $trip->margin_profit_percentage,
            $trip->payout_status,
            $trip->delivery_status,
            $trip->loading_tonnage_value,
            $trip->offloading_tonnage_value,
            $trip->days_in_transit,
            $trip->days_delivered,
            $trip->flagged,
            $trip->flagged_by,
            $trip->trip_status_id,
            $trip->way_bill_status_id,
        );
        $object->loading_date = $trip->loading_date ? Carbon::parse($trip->loading_date) : null;
        $object->delivery_date = $trip->delivery_date ? Carbon::parse($trip->delivery_date) : null;
        $object->completed_date = $trip->completed_date ? Carbon::parse($trip->completed_date) : null;
        return $object;
    }

    public function cleanObject($object): object
    {
        // Strips only null values
        return (object)array_filter((array)$object, function ($val) {
            return !is_null($val);
        });
    }
}
