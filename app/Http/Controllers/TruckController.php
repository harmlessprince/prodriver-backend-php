<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\TruckDto;
use App\Http\Requests\TruckRequest;
use App\Models\Document;
use App\Models\Truck;
use App\Models\User;
use App\Services\CloudinaryFileService;
use App\Services\TruckService;
use App\Utils\DocumentType;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TruckController extends Controller
{
    public function __construct(
        private readonly CloudinaryFileService $cloudinaryFileService,
        private readonly TruckService          $truckService
    ) {
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
        $this->authorize('viewAny', Truck::class);

        $truckQuery = Truck::query()->with(Truck::RELATIONS);
        $truckQuery = $truckQuery->filter($request->all(), $truckQuery);
        $truckQuery = $truckQuery->search();
        $totalTrucksQuery = Truck::query();
        $user = $request->user();
        if ($user->user_type === User::USER_TYPE_TRANSPORTER) {
            $totalTrucksQuery = $totalTrucksQuery->where('transporter_id', $user->id);
            $truckQuery = $truckQuery->where('transporter_id', $user->id);
        }
        $trucks = $truckQuery->latest('created_at')->paginate(request('per_page'));
        return $this->respondSuccess(['trucks' => $trucks, 'meta' => ['total_trucks' => $totalTrucksQuery->count()]], 'All trucks fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TruckRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(TruckRequest $request): JsonResponse
    {
        $this->authorize('create', Truck::class);
        $user = $request->user();
        $driver_id = $request->driver_id;
        if ($request->has('transporter_id')) {
            $transporter_id = $request->transporter_id;
        } else {
            $transporter_id = $user->id;
        }
        $driverExists = User::query()->whereHas('drivers', function ($query) use ($driver_id) {
            $query->where('id', $driver_id);
        })->where('id', $transporter_id)->exists();
        if (!$driverExists) {
            $this->respondError('The supplied driver doest not belong to the supplied truck owner');
        }
        $truckDto = TruckDto::fromApiRequest($request, $transporter_id, $driver_id);
        $truck = $this->truckService->createTruck($truckDto);
        $this->createTruckDocs($truckDto, $truck);
        return $this->respondSuccess(['truck' => $truck], 'Truck created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param Truck $truck
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Truck $truck): JsonResponse
    {
        $this->authorize('view', $truck);
        $user = request()->user();
        $relations = [...Truck::DOCUMENT_RELATIONS, ...Truck::NON_DOCUMENT_RELATIONS];
        if ($user->id === $truck->transporter_id) {
            unset($relations['truckOwner']);
        }
        $truck = $truck->load($relations);
        return $this->respondSuccess(['truck' => $truck], 'Truck fetched successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TruckRequest $request
     * @param Truck $truck
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(TruckRequest $request, Truck $truck): JsonResponse
    {
        $this->authorize('update', $truck);
        $user = $request->user();
        $truckDto = TruckDto::fromApiRequest($request, $truck->transporter_id, $truck->driver_id);
        $attributes = $request->validated();
        $truckTableColumns = $this->getTruckTableColumns();
        $truckData = [];
        foreach ($attributes as $key => $value) {
            if (!is_null($value)) {
                if (in_array($key, $truckTableColumns)) {
                    $truckData[$key] = $value;
                }
            }
        }
        $truck->update($truckData);
        $this->createTruckDocs($truckDto, $truck);
        return $this->respondSuccess([], 'Truck updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Truck $truck
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Truck $truck): JsonResponse
    {
        $this->authorize('delete', $truck);
        $truck->delete();
        return $this->respondSuccess([], 'Truck deleted');
    }

    private function getTruckTableColumns(): array
    {
        //cache the columns for 6hrs
        return Cache::remember('truck_table_columns', 21600, function () {
            $truck = new Truck();
            $tableName = $truck->getTable();
            return Schema::getColumnListing($tableName);
        });
    }
    public function createTruckDocs(TruckDto $truckDto, Truck $truck)
    {
        if ($truckDto->picture_id) {
            $this->truckService->syncTruckPictures($truck, $truckDto->picture_id);
        }
        if ($truckDto->proof_of_ownership_id) {
            $this->truckService->syncTruckProofOfOwnerShipDoc($truck, $truckDto->proof_of_ownership_id);
        }
        if ($truckDto->road_worthiness_id) {
            $this->truckService->syncTruckRoadWorthinessDoc($truck, $truckDto->road_worthiness_id);
        }
        if ($truckDto->license_id) {
            $this->truckService->syncTruckLicenseDoc($truck, $truckDto->license_id);
        }
        if ($truckDto->insurance_id) {
            $this->truckService->syncTruckInsuranceDoc($truck, $truckDto->insurance_id);
        }
    }
}
