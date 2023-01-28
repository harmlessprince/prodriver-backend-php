<?php

namespace App\DataTransferObjects;

use App\Http\Requests\TripRequest;
use App\Http\Requests\TruckRequest;
use Carbon\Carbon;

class TripDto
{

    public function __construct(
        public readonly ?int    $trip_id,
        public readonly ?int    $approved_by,
        public readonly ?int    $matched_by,
        public readonly ?int    $declined_by,
        public readonly ?int    $account_manager_id,
        public readonly ?int    $driver_id,
        public readonly ?int    $truck_id,
        public readonly ?int    $order_id,
        public readonly ?int    $cargo_owner_id,
        public readonly ?int    $transporter_id,
        public readonly ?int    $way_bill_picture_id,
        public readonly ?float  $total_payout,
        public readonly ?float  $advance_payout,
        public readonly ?float  $margin_profit_amount,
        public readonly ?float  $margin_profit_percentage,
        public readonly ?Carbon $loading_date,
        public readonly ?Carbon $delivery_date,
        public readonly ?int    $trip_status_id,
        public readonly ?int    $way_bill_status_id,
    )
    {
    }

//      public static function fromApiRequest(TripRequest $request, int $truckOwnerId, int $driver_id): TripDto
//      {
// //         return new self (
//              // truck_owner_id: $truckOwnerId,
//              // driver_id: $driver_id,
//              // truck_type_id: $request->truck_type_id,
//              // tonnage_id: $request->tonnage_id,
//              // chassis_number: $request->chassis_number,
//              // plate_number: $request->plate_number,
//              // maker: $request->maker,
//              // model: $request->model,
//              // registration_number: $request->registration_number,
//              // picture_id: $request->picture_id,
//              // proof_of_ownership_id: $request->proof_of_ownership_id,
//              // road_worthiness_id: $request->road_worthiness_id,
//              // license_id: $request->validated('license_id'),
//              // insurance_id: $request->insurance_id
// //         );
//      }
//    public static function cleanRequest(TruckRequest $request, int $truckOwnerId, int $driver_id)
//    {
//        $object  =  new self (
//            truck_owner_id: $truckOwnerId,
//            driver_id: $driver_id,
//            truck_type_id: $request->truck_type_id,
//            tonnage_id: $request->tonnage_id,
//            chassis_number: $request->chassis_number,
//            plate_number: $request->plate_number,
//            maker: $request->maker,
//            model: $request->model,
//            registration_number: $request->registration_number,
//            picture_id: $request->picture_id,
//            proof_of_ownership_id: $request->proof_of_ownership_id,
//            road_worthiness_id: $request->road_worthiness_id,
//            license_id: $request->license_id,
//            insurance_id: $request->insurance_id
//        );
//    }

    public function cleanObject($object): object
    {
        // Strips only null values
        return (object)array_filter((array)$object, function ($val) {
            return !is_null($val);
        });
    }
}
