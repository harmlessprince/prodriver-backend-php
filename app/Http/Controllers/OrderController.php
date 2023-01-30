<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Truck;
use App\Models\User;
use App\Services\CloudinaryFileService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    public function __construct(private readonly CloudinaryFileService $cloudinaryFileService)
    {
    }

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
        $ordersQuery = Order::query()->with(['tonnage', 'truckTypes', 'cargoOwner:id,first_name,last_name,middle_name,phone_number,email']);
        if ($user->user_type == User::USER_TYPE_CARGO_OWNER) {
            $ordersQuery = $ordersQuery->where('user_id', $user->id);
        }
        if ($user->user_type == User::USER_TYPE_TRANSPORTER) {
            $ordersQuery = $ordersQuery->where('declined_by', null)
                ->where('approved_by', null)
                ->where('matched_by', null)
                ->where('status', Order::PENDING);
        }
        $orders = $ordersQuery->simplePaginate();
        return $this->respondSuccess(['requests' => $orders], 'Truck requests fetched');
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
        $user = $request->user();
        $truckTypeIds = [];
        $productPictures = [];
        if (array_key_exists('product_pictures', $data)) {
            $productPictures = $data['product_pictures'];
            unset($data['product_pictures']);
        }
        if (array_key_exists('truck_type_ids', $data)) {
            $truckTypeIds = $data['truck_type_ids'];
            unset($data['truck_type_ids']);
        }

        $data['created_by'] = $user->id;
        if ($user->user_type !== User::USER_TYPE_ADMIN) {
            $data['cargo_owner_id'] = $user->id;
        }
        $data['potential_payout'] = $data['amount_willing_to_pay'] - ((10 / 100) *  $data['amount_willing_to_pay']);
        /** @var  Order $truckRequest */
        // Log::info($data);
        $truckRequest = Order::query()->create($data);
        if (count($truckTypeIds) > 0) {
            $truckRequest->truckTypes()->sync($truckTypeIds);
        }
        if (count($productPictures) > 0) {
            $this->cloudinaryFileService->takeOwnerShip($productPictures, Truck::MORPH_NAME, $truckRequest->id);
        }
        $truckRequest = $truckRequest->load(['tonnage', 'truckTypes']);
        return $this->respondSuccess(['order' => $truckRequest], 'Truck request created successfully');
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
        $order = $truckRequest->load(Order::RELATIONS);
        return $this->respondSuccess(['order' => $order], 'Truck request fetched successfully');
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
        if (array_key_exists('truck_type_ids', $data)) {
            $truckTypeIds = $data['truck_type_ids'];
            $truckRequest->truckTypes()->sync($truckTypeIds);
            unset($data['truck_type_ids']);
        }
        /** @var  Order $newOrder */
        $truckRequest->update($data);

        return $this->respondSuccess([], 'Request updated successfully');
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
        return $this->respondSuccess([], 'Request deleted successfully');
    }

    public function cancelRequest(Request $request, Order $order)
    {
        //TODO Authorize user
        /** @var User $user */
        $user = $request->user();
        $order->status = Order::CANCELLED;
        $order->cancelled_by = $user->id;
        $order->save();
    }

    public function approveRequest(Request $request, Order $order)
    {
        //TODO Authorize user
        /** @var User $user */
        $user = $request->user();
        $order->status = Order::APPROVED;
        $order->approved_by = $user->id;
        $order->save();
    }

    public function declineRequest(Request $request, Order $order)
    {
        //TODO Authorize user
        /** @var User $user */
        $user = $request->user();
        $order->status = Order::DECLINED;
        $order->declined_by = $user->id;
        $order->save();
    }

    public function acceptOrder(Request $request, Order $order)
    {
        $this->validate($request, [
            // 'amount' => ['nullable', '']
        ]);
        //TODO Authorize user
        /** @var User $user */
        $user = $request->user();
    }

    public function matchRequest(Request $request, Order $order)
    {
        //TODO Authorize user
        /** @var User $user */
        $user = $request->user();
        $order->status = Order::MATCHED;
        $order->matched_by = $user->id;
        $order->save();
    }
}
