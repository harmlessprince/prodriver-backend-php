<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaybillStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class WaybillStatusController extends Controller
{
    public function index()
    {
        return $this->respondSuccess(['waybill_statuses' => WaybillStatus::get()]);
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

        $waybillStatus = WaybillStatus::create(['name' => $request->input('name'), 'status' => $request->input('status')]);
        return $this->respondSuccess(['waybill_status' => $waybillStatus], 'Status created');
    }
    /**
     * @throws ValidationException
     */
    public  function  update(Request $request, WaybillStatus $waybillStatus): JsonResponse
    {
        $this->validate($request, [
            'name' => ['sometimes', 'string', Rule::unique('waybill_statuses', 'name')->ignore($waybillStatus->id)],
            'status' => ['sometimes', 'boolean']
        ]);

        $waybillStatus->update(['name' => $request->input('name', $waybillStatus->name), 'status' => $request->input('status', $waybillStatus->status)]);
        return $this->respondSuccess(['waybill_status' => $waybillStatus->fresh()], 'Status updated');
    }
}
