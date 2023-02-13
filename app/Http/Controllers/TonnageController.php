<?php

namespace App\Http\Controllers;

use App\Models\Tonnage;
use App\Models\TruckType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TonnageController extends Controller
{

    public function  index(Request $request): JsonResponse
    {
        $tonnagesQuery = Tonnage::query()->search();
        if ($request->query('shouldPaginate') === 'yes') {
            $tonnages = $tonnagesQuery->paginate();
        } else {
            $tonnages = $tonnagesQuery->get();
        }
        return $this->respondSuccess(['tonnages' => $tonnages], 'Tonnages fetched successfully');
    }
    /**
     * @throws ValidationException
     */
    public  function  store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => ['required', 'string', Rule::unique('tonnages', 'name')],
            'value' => ['required', 'integer', Rule::unique('tonnages', 'value')],
        ]);
        $tonnage =  Tonnage::query()->create(['name' => $request->input('name'), 'value' => $request->input('value')]);
        return $this->respondSuccess(['tonnage' => $tonnage], 'Tonnage created');
    }
    /**
     * @throws ValidationException
     */
    public  function  update(Request $request, Tonnage $tonnage): JsonResponse
    {
        $this->validate($request, [
            'name' => ['sometimes', 'string', Rule::unique('tonnages', 'name')->ignore($tonnage->id)],
            'value' => ['sometimes', 'integer', Rule::unique('tonnages', 'value')->ignore($tonnage->id)]
        ]);

        $tonnage->update(['name' => $request->input('name', $tonnage->name), 'value' => $request->input('value', $tonnage->value)]);
        return $this->respondSuccess([], 'Tonnage updated');
    }

    public function destroy(Request $request, Tonnage $tonnage): JsonResponse
    {
        $tonnage->delete();
        return $this->respondSuccess([], 'Tonnage deleted successfully');
    }
}
