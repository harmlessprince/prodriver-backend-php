<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\AcceptOrderDto;
use App\DataTransferObjects\ApproveAcceptedOrderDto;
use App\DataTransferObjects\MatchOrderDto;
use App\Http\Requests\OrderRequest;
use App\Models\AcceptedOrder;
use App\Models\Order;
use App\Models\Truck;
use App\Models\User;
use App\Repositories\Eloquent\Repository\OrderRepository;
use App\Repositories\Eloquent\Repository\TruckRepository;
use App\Services\CloudinaryFileService;
use App\Services\OrderServices;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    private TruckRepository $truckRepository;
    private OrderRepository $orderRepository;
    private CloudinaryFileService $cloudinaryFileService;
    private OrderServices $orderServices;


    public function __construct(
        CloudinaryFileService $cloudinaryFileService,
        TruckRepository       $truckRepository,
        OrderRepository       $orderRepository,
        OrderServices         $orderServices,
    )
    {
        $this->orderRepository = $orderRepository;
        $this->truckRepository = $truckRepository;
        $this->cloudinaryFileService = $cloudinaryFileService;
        $this->orderServices = $orderServices;
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
        $orders = $this->orderRepository->paginatedOrders($user, ['*'], ['tonnage', 'truckTypes', 'cargoOwner:id,first_name,last_name,middle_name,phone_number,email']);
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
        $data['potential_payout'] = $data['amount_willing_to_pay'] - ((10 / 100) * $data['amount_willing_to_pay']);
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

    /**
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function acceptOrder(Request $request, Order $order): JsonResponse
    {
        $this->authorize('accept', $order);
        $this->validate($request, [
            'amount' => ['required', 'numeric', 'min:1'],
            'truck_id' => ['required', 'numeric', Rule::exists('trucks', 'id')]
        ]);
        /** @var User $user */
        $user = $request->user();
        if ($this->orderServices->hasAcceptedOrder($order->id, $user->id, $request->input('truck_id'))) {
            return $this->respondForbidden('You can not accept a order more than once');
        }
        $truckIsOnTrip = $this->truckRepository->truckIsOnTrip($request->truck_id);

        if ($truckIsOnTrip) {
            return $this->respondSuccess([], 'The selected truck is on a trip');
        }
        $acceptOrder = new AcceptOrderDto(
            $order->id,
            $request->input('truck_id'),
            $user->id,
            $request->input('amount'),
        );
        $this->orderServices->acceptOrder($acceptOrder);
        return $this->respondSuccess([], 'Your interest in this order has been registered');
    }

    /**
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function matchOrder(Request $request, Order $order): JsonResponse
    {
        $this->authorize('match', $order);
        $this->validate($request, [
            'amount' => ['required', 'numeric', 'min:1'],
            'transporter_id' => ['required', 'numeric', Rule::exists('users', 'id')->where('user_type', User::USER_TYPE_TRANSPORTER)],
            'truck_id' => ['required', 'numeric', Rule::exists('trucks', 'id')],
        ]);
        /** @var User $user */
        $user = $request->user();
        if ($this->orderServices->hasAcceptedOrder($order->id, $request->input('transporter_id'), $request->input('truck_id'))) {
            return $this->respondForbidden('The supplied transporter has already been matched with the supplied order');
        }
        $matchOrderDto = new MatchOrderDto($order->id, $request->input('truck_id'), $request->input('transporter_id'), $user->id, $request->input('amount'));
        $this->orderServices->matchOrder($matchOrderDto);
        return $this->respondSuccess([], 'Order matched successfully');
    }

    /**
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function approveOrder(Request $request, AcceptedOrder $acceptedOrder)
    {
        $this->authorize('approve', $acceptedOrder);
        $this->validate($request, [
            'account_manger_id' => ['required', 'integer', Rule::exists('users', 'id')->where('user_type', User::USER_TYPE_ACCOUNT_MANAGER)],
            'total_payout' => ['required', 'numeric'],
            'advance_payout' => ['required', 'numeric'],
            'loading_date' => ['required', 'date', 'after:' . Carbon::now()->toDateString()],
            'delivery_date' => ['required', 'date', 'after:loading_date'],
        ]);
        $acceptedOrderDto = AcceptOrderDto::fromModel($acceptedOrder);
//        $approveAcceptedOrderDto = new ApproveAcceptedOrderDto(
//            $request->input('total_payout'),
//
//        );
//        $this->orderServices->approveAnAcceptedOrderRequest();

    }

   private function generateTripID (AcceptOrderDto $acceptOrderDto){
//        return $acceptOrderDto->order_id . "/" . $acceptOrderDto->
   }
}
