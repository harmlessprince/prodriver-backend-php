<?php

namespace App\Http\Controllers;

use App\Models\TruckType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TruckTypeController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $truckTypeQuery = TruckType::query()->withCount('trucks');
        $truckTypes = $truckTypeQuery->get();
        return $this->respondSuccess(['truck_types' => $truckTypes], 'Truck types fetched');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, ['name' => ['required', 'string']]);
        TruckType::query()->create(['name' => $request->input('name')]);
        return $this->respondSuccess([], 'Truck type created');
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, TruckType $truckType): JsonResponse
    {
        $this->validate($request, ['name' => ['required', 'string']]);
        $truckType->update(['name' => $request->input('name')]);
        return $this->respondSuccess([], 'Truck type updated');
    }

    public function destroy(Request $request, TruckType $truckType): JsonResponse
    {
        $truckType->delete();
        return $this->respondSuccess([], 'Truck type deleted successfully');
    }
}
