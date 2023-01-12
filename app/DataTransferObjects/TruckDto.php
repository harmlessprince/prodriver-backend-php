<?php

namespace App\DataTransferObjects;

use App\Http\Requests\TruckRequest;

class TruckDto
{
    public function __construct(
        public readonly ?int    $truck_owner_id,
        public readonly ?int    $driver_id,
        public readonly ?int    $truck_type_id,
        public readonly ?int    $tonnage_id,
        public readonly ?string $chassis_number,
        public readonly ?string $maker,
        public readonly ?string $model,
        public readonly ?string $registration_number,
        public readonly ?int   $picture_id,
        public readonly ?int   $proof_of_ownership_id,
        public readonly ?int   $road_worthiness_id,
        public readonly ?int   $license_id,
        public readonly ?int   $insurance_id
    )
    {
    }

    public static function fromApiRequest(TruckRequest $request, int $truckOwnerId, int $driver_id): TruckDto
    {
        return new self (
            truck_owner_id: $truckOwnerId,
            driver_id: $driver_id,
            truck_type_id: $request->truck_type_id,
            tonnage_id: $request->tonnage_id,
            chassis_number: $request->chassis_number,
            maker: $request->maker,
            model: $request->model,
            registration_number: $request->registration_number,
            picture_id: $request->picture_id,
            proof_of_ownership_id: $request->proof_of_ownership_id,
            road_worthiness_id: $request->road_worthiness_id,
            license_id: $request->validated('license_id'),
            insurance_id: $request->insurance_id
        );
    }
    public static function cleanRequest(TruckRequest $request, int $truckOwnerId, int $driver_id)
    {
        $object  =  new self (
            truck_owner_id: $truckOwnerId,
            driver_id: $driver_id,
            truck_type_id: $request->truck_type_id,
            tonnage_id: $request->tonnage_id,
            chassis_number: $request->chassis_number,
            maker: $request->maker,
            model: $request->model,
            registration_number: $request->registration_number,
            picture_id: $request->picture_id,
            proof_of_ownership_id: $request->proof_of_ownership_id,
            road_worthiness_id: $request->road_worthiness_id,
            license_id: $request->license_id,
            insurance_id: $request->insurance_id
        );
    }

    public function cleanObject($object): object
    {
        // Strips only null values
        return (object) array_filter((array) $object, function ($val) {
            return !is_null($val);
        });
    }
}
