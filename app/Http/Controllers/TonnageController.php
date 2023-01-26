<?php

namespace App\Http\Controllers;

use App\Models\Tonnage;
use App\Models\TruckType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TonnageController extends Controller
{

    public function  index(Request $request): JsonResponse
    {
        return $this->respondSuccess(['tonnages' => Tonnage::query()->get()], 'Tonnages fetched successfully');
    }
    /**
     * @throws ValidationException
     */
    public  function  store(Request $request): JsonResponse
    {
        $this->validate($request, ['name' => ['required', 'string']]);
        $tonnage =  Tonnage::query()->create(['name' => $request->input('name')]);
        return $this->respondSuccess(['tonnage' => $tonnage], 'Tonnage created');
    }
    /**
     * @throws ValidationException
     */
    public  function  update(Request $request, Tonnage $tonnage): JsonResponse
    {
        $this->validate($request, ['name' => ['required', 'string']]);
        $tonnage->update(['name' => $request->input('name')]);
        return $this->respondSuccess([], 'Tonnage updated');
    }

    public function destroy(Request $request, Tonnage $tonnage): JsonResponse
    {
        $tonnage->delete();
        return $this->respondSuccess([], 'Tonnage deleted successfully');
    }
}
