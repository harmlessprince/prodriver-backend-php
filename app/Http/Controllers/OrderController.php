<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);
        /** @var User $user */
        $user = $request->user();
        $ordersQuery = Order::query()->with(['tonnage', 'truckTypes']);
        if ($user->user_type == User::USER_TYPE_CARGO_OWNER){
            $ordersQuery = $ordersQuery->where('user_id', $user->id);
        }
        if ($user->user_type == User::USER_TYPE_TRANSPORTER){
            $ordersQuery = $ordersQuery->where('declined_by', null)
                ->where('approved_by', null)
                ->where('matched_by', null)
                ->where('status', Order::PENDING);
        }
        $orders = $ordersQuery->simplePaginate();
        return  $this->respondSuccess(['requests' => $orders ], 'Truck requests fetched');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);
        $data = $request->validated();
        $truckTypeIds = $data['truck_type_ids'];
        unset($data['truck_type_ids']);
        $data = $data + [
                'created_by' => $request->user()->id,
            ];
        /** @var  Order $truckRequests */
        $truckRequests = Order::query()->create($data);
        $truckRequests->truckTypes()->sync($truckTypeIds);
        return $this->respondSuccess(['order' => $truckRequests->load(['tonnage', 'truckTypes'])], 'Truck request created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param Order $truckRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Order $truckRequest): JsonResponse
    {
        $this->authorize('view', $truckRequest);
        $order =$truckRequest->load(Order::RELATIONS);
        return $this->respondSuccess(['order' => $order ], 'Truck request fetched successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OrderRequest $request
     * @param Order $truckRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(OrderRequest $request, Order $truckRequest): JsonResponse
    {
        $this->authorize('update', $truckRequest);
        $data = $request->validated();
        $truckTypeIds = $data['truck_type_ids']  ?? [];
        unset($data['truck_type_ids']);
        /** @var  Order $newOrder */
        $truckRequest->update($data);
        $truckRequest->truckTypes()->sync($truckTypeIds);
        return  $this->respondSuccess([], 'Request updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Order $truckRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Order $truckRequest): JsonResponse
    {
        $this->authorize('delete', $truckRequest);
        $truckRequest->delete();
        return  $this->respondSuccess([], 'Request updated successfully');
    }
}
