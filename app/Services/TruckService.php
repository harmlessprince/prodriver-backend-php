<?php

namespace App\Services;
use App\DataTransferObjects\TruckDto;
use App\Models\Document;
use App\Models\Truck;
use App\Services\CloudinaryFileService;
use App\Utils\DocumentType;
use Illuminate\Database\Eloquent\Model;

class TruckService
{

    public function __construct(private readonly CloudinaryFileService $cloudinaryFileService)
    {
    }

    public function createTruck(TruckDto $truckDto): Truck
    {
        /** @var Truck */
        return Truck::query()->create([
            'transporter_id' => $truckDto->transporter_id,
            'driver_id' => $truckDto->driver_id,
            'truck_type_id' => $truckDto->truck_type_id,
            'registration_number' => $truckDto->registration_number,
            'tonnage_id' => $truckDto->tonnage_id,
            'chassis_number' => $truckDto->chassis_number,
            'plate_number' => $truckDto->plate_number,
            'maker' => $truckDto->maker,
            'model' => $truckDto->model,
        ]);
    }
    public function syncTruckPictures(Truck $truck, $picture_id)
    {
        $pictureDoc = $truck->picture()->updateOrCreate(
            [
                'user_id' => $truck->transporter_id,
                'document_type' => DocumentType::TRUCK_PICTURE['key'],
                'document_name' => DocumentType::TRUCK_PICTURE['name'],
            ],
            [
                'file_id' => $picture_id,
                'status' => 'submitted'
            ]
        );
        $this->cloudinaryFileService->takeOwnerShip([$picture_id], Document::MORPH_NAME, $pictureDoc->id);
    }

    public function syncTruckProofOfOwnerShipDoc(Truck $truck, int $proof_of_ownership_id)
    {
        $proofDoc = $truck->proofOfOwnership()->updateOrCreate(
            [
                'user_id' => $truck->transporter_id,
                'document_type' => DocumentType::TRUCK_PROOF_OF_OWNERSHIP['key'],
                'document_name' => DocumentType::TRUCK_PROOF_OF_OWNERSHIP['name'],
            ],
            [
                'file_id' => $proof_of_ownership_id,
                'status' => 'submitted'
            ]
        );
        $this->cloudinaryFileService->takeOwnerShip([$proof_of_ownership_id], Document::MORPH_NAME, $proofDoc->id);
    }

    public function syncTruckRoadWorthinessDoc(Truck $truck, int $road_worthiness_id)
    {
        $roadDoc = $truck->roadWorthiness()->updateOrCreate(
            [
                'user_id' => $truck->transporter_id,
                'document_type' => DocumentType::TRUCK_ROAD_WORTHINESS['key'],
                'document_name' => DocumentType::TRUCK_ROAD_WORTHINESS['name'],
            ],
            [
                'file_id' => $road_worthiness_id,
                'status' => 'submitted'
            ]
        );
        $this->cloudinaryFileService->takeOwnerShip([$road_worthiness_id], Document::MORPH_NAME, $roadDoc->id);
    }
    public function syncTruckLicenseDoc(Truck $truck, int $license_id)
    {
        $licenseDoc = $truck->license()->updateOrCreate(
            [
                'user_id' => $truck->transporter_id,
                'document_type' => DocumentType::TRUCK_LICENSE['key'],
                'document_name' => DocumentType::TRUCK_LICENSE['name'],
            ],
            [

                'file_id' => $license_id,
                'status' => 'submitted'
            ]
        );
        $this->cloudinaryFileService->takeOwnerShip([$license_id], Document::MORPH_NAME, $licenseDoc->id);
    }

    public function syncTruckInsuranceDoc(Truck $truck, int $insurance_id)
    {
        $insuranceDoc = $truck->insurance()->updateOrCreate(
            [
                'user_id' => $truck->transporter_id,
                'document_type' => DocumentType::TRUCK_INSURANCE['key'],
                'document_name' => DocumentType::TRUCK_INSURANCE['name'],
            ],
            [
                'file_id' => $insurance_id,
                'status' => 'submitted'
            ]
        );
        $this->cloudinaryFileService->takeOwnerShip([$insurance_id], Document::MORPH_NAME, $insuranceDoc->id);
    }
}
