<?php

namespace App\Http\Controllers;

use App\Models\TripStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TripStatusController extends Controller
{

    public function index()
    {
        return $this->respondSuccess(['trips_statuses' => TripStatus::get()]);
    }

    /**
     * @throws ValidationException
     */
    public  function  store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => ['required', 'string'],
            'status' => ['sometimes', 'boolean']
        ]);

        $tripStatus = TripStatus::create(['name' => $request->input('name'), 'status' => $request->input('status')]);
        return $this->respondSuccess(['trip_status' => $tripStatus], 'Status created');
    }
    /**
     * @throws ValidationException
     */
    public  function  update(Request $request, TripStatus $tripStatus): JsonResponse
    {
        $this->validate($request, [
            'name' => ['sometimes', 'string', Rule::unique('trip_statuses', 'name')->ignore($tripStatus->id)],
            'status' => ['sometimes', 'boolean']
        ]);

        $tripStatus->update(['name' => $request->input('name', $tripStatus->name), 'status' => $request->input('status', $tripStatus->status)]);
        return $this->respondSuccess(['trip_status' => $tripStatus->fresh()], 'Status updated');
    }
}
