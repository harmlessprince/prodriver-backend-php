<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Trip;
use App\Models\TripWaybillPicture;
use App\Models\WaybillStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TripWaybillPictureController extends Controller
{
    public function store(Request $request, Trip $trip)
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
            'way_bill_status_id' => WaybillStatus::where('name', WaybillStatus::STATUS_RECEIVED)->first()->id,
        ]);


        return $this->respondSuccess(['waybill_picture' =>  $waybillPicture], 'Trip waybill status updated');
    }

    public function update(Request $request, TripWaybillPicture $tripWaybillPicture)
    {

        $user = request()->user();
        $fileExists = Rule::exists(File::class, 'id')
            ->where('type', File::TYPE_IMAGE)
            ->where('creator_id', $user->id);

        $this->validate($request, [
            'picture_id' => ['sometimes', 'integer',  $fileExists],
            'status_id' => ['sometimes', 'integer', 'exists:waybill_statuses,id']
        ]);

        $waybillPicture =  $tripWaybillPicture->update([
            'picture_id' => $request->input('picture_id', $tripWaybillPicture->picture_id),
            'uploaded_by' => auth()->id(),
            'way_bill_status_id' => $request->input('status_id', $tripWaybillPicture->way_bill_status_id),
        ]);

        return $this->respondSuccess(['waybill_picture' =>  $waybillPicture], 'Trip waybill status updated');
    }
}
