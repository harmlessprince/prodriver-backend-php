<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request)
    {
        $data = $request->validated();
        $truckTypeIds = $data['truck_type_ids'];
        unset($data['truck_type_ids']);
        $data = $data + [
                'created_by' => $request->user()->id,
            ];
        /** @var  Order $newOrder */
        $newOrder = Order::query()->create($data);
        $newOrder->truckTypes()->sync($truckTypeIds);
        return $this->respondSuccess(['order' => $newOrder], 'Truck request created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Order $order
     * @return Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
