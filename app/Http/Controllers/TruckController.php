<?php

namespace App\Http\Controllers;

use App\Http\Requests\TruckRequest;
use App\Models\Document;
use App\Models\Truck;
use App\Models\User;
use App\Services\CloudinaryFileService;
use App\Utils\DocumentType;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    public function __construct(private readonly CloudinaryFileService $cloudinaryFileService)
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TruckRequest $request)
    {

        // $data = $request->validated();
        $user = $request->user();
        $driver_id = $request->driver_id;
        if ($request->has('truck_owner_id')) {
            $truck_owner_id = $request->truck_owner_id;
        } else {
            $truck_owner_id = $user->id;
        }
        $driverExists = User::query()->whereHas('drivers', function ($query) use ($driver_id) {
            $query->where('id', $driver_id);
        })->where('id', $truck_owner_id)->exists();
        if (!$driverExists) {
           $this->respondError('The supplied driver doest not belong to the supplied truck owner');
        }
        $truck = Truck::query()->create([
            'truck_owner_id' => $truck_owner_id,
            'driver_id' => $driver_id,
            'truck_type_id' => $request->truck_type_id,
            'registration_number' => $request->registration_number,
            'tonnage_id' => $request->tonnage_id,
            'chassis_number' => $request->chassis_number,
            'maker' => $request->maker,
            'model' => $request->model,
        ]);
        if ($request->has('picture_id')) {
           $pictureDoc = $truck->pictures()->create([
                'user_id' => $truck->truck_owner_id,
                'file_id' => $request->picture_id,
                'document_type' => DocumentType::TRUCK_PICTURE['key'],
                'document_name' => DocumentType::TRUCK_PICTURE['name'],
                'status' => 'submitted'
            ]);
            $this->cloudinaryFileService->takeOwnerShip([$request->picture_id], Document::MORPH_NAME, $pictureDoc->id);
        }
        if ($request->has('proof_of_ownership_id')) {
            $proofDoc = $truck->proofOfOwnership()->create([
                'user_id' => $truck->truck_owner_id,
                'file_id' => $request->proof_of_ownership_id,
                'document_type' => DocumentType::TRUCK_PROOF_OF_OWNERSHIP['key'],
                'document_name' => DocumentType::TRUCK_PROOF_OF_OWNERSHIP['name'],
                'status' => 'submitted'
            ]);
            $this->cloudinaryFileService->takeOwnerShip([$request->proof_of_ownership_id], Document::MORPH_NAME, $proofDoc->id);
        }
        if ($request->has('road_worthiness_id')) {
            $roadDoc = $truck->roadWorthiness()->create([
                'user_id' => $truck->truck_owner_id,
                'file_id' => $request->road_worthiness_id,
                'document_type' => DocumentType::TRUCK_ROAD_WORTHINESS['key'],
                'document_name' => DocumentType::TRUCK_ROAD_WORTHINESS['name'],
                'status' => 'submitted'
            ]);
            $this->cloudinaryFileService->takeOwnerShip([$request->road_worthiness_id], Document::MORPH_NAME, $roadDoc->id);
        }
        if ($request->has('license_id')) {
            $licenseDoc = $truck->license()->create([
                'user_id' => $truck->truck_owner_id,
                'file_id' => $request->license_id,
                'document_type' => DocumentType::TRUCK_LICENSE['key'],
                'document_name' => DocumentType::TRUCK_LICENSE['name'],
                'status' => 'submitted'
            ]);
            $this->cloudinaryFileService->takeOwnerShip([$request->license_id], Document::MORPH_NAME, $licenseDoc->id);
        }
        if ($request->has('insurance_id')) {
            $insuranceDoc = $truck->insurance()->create([
                'user_id' => $truck->truck_owner_id,
                'file_id' => $request->insurance_id,
                'document_type' => DocumentType::TRUCK_INSURANCE['key'],
                'document_name' => DocumentType::TRUCK_INSURANCE['name'],
                'status' => 'submitted'
            ]);
            $this->cloudinaryFileService->takeOwnerShip([$request->license_id], Document::MORPH_NAME, $insuranceDoc->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Truck  $truck
     * @return \Illuminate\Http\Response
     */
    public function show(Truck $truck)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Truck  $truck
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Truck $truck)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Truck  $truck
     * @return \Illuminate\Http\Response
     */
    public function destroy(Truck $truck)
    {
        //
    }
}
