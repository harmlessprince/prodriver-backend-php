<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverRequest;
use App\Models\Company;
use App\Models\Driver;
use App\Models\File;
use App\Models\User;
use App\Services\CloudinaryFileService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DriverController extends Controller
{

    public function __construct(protected readonly CloudinaryFileService $cloudinaryFileService)
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
        $this->authorize('viewAny', Driver::class);
        /** @var User $user */
        $user = $request->user();
        $totalDriversQuery = Driver::query();
        $driversQuery = Driver::query()->search()->with(['picture', 'licensePicture', 'user']);
        if ($user->user_type == User::USER_TYPE_TRANSPORTER) {
            $totalDriversQuery = $totalDriversQuery->where('user_id', $user->id);
            $driversQuery = $driversQuery->where('user_id', $user->id);
        }
        $drivers = $driversQuery->latest()->paginate();
        return $this->respondSuccess(['drivers' => $drivers, 'meta' => ['total_drivers' => $totalDriversQuery->count()]], 'Drivers retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DriverRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(DriverRequest $request): JsonResponse
    {
        $this->authorize('create', Driver::class);
        /** @var User $user */
        $user = $request->user();
        $data = $request->validated();
        if ($user->user_type === User::USER_TYPE_TRANSPORTER) {
            // if ($user->company()->exists()) {
            $data['user_id'] = $user->id;
            // $data['company_id'] = $user->company->id;
            // } else {
            //     return $this->respondError('You are not allowed to create a driver until you create a company profile');
            // }
        } else {
            $transporterUser = User::query()->findOrFail($request->user_id);
            if ($transporterUser->user_type != User::USER_TYPE_TRANSPORTER) {
                $this->cloudinaryFileService->deleteMultipleFilesById([$request->picture_id, $request->license_picture_id]);
                return  $this->respondError('The supplied user id does not belong to a transporter', 403);
            }
            // $company = Company::query()->findOrFail($request->company_id);
            // if ($user->id != $company->user_id) {
            //     return $this->respondError('The provided company does not belong to the supplied user id');
            // }
        }
        $driver = Driver::query()->create($data);
        if ($request->has('picture_id')) {
            $this->cloudinaryFileService->takeOwnerShip([$request->picture_id], Driver::MORPH_NAME, $driver->id);
        }
        if ($request->has('license_picture_id')) {
            $this->cloudinaryFileService->takeOwnerShip([$request->license_picture_id], Driver::MORPH_NAME, $driver->id);
        }
        return $this->respondSuccess([], 'Driver created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param Driver $driver
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Driver $driver): JsonResponse
    {
        $this->authorize('view', $driver);
        $driver = $driver->load('picture', 'licensePicture', 'user');
        return $this->respondSuccess(['driver' => $driver], 'Driver fetched');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DriverRequest $request
     * @param Driver $driver
     * @return JsonResponse
     */
    public function update(DriverRequest $request, Driver $driver): JsonResponse
    {
        $this->authorize('update', $driver);
        if ($request->has('picture_id')) {
            if ($driver->picture()->exists()) {
                if (($driver->picture->id != $request->picture_id)) {
                    $file = $driver->picture;
                    $this->cloudinaryFileService->deleteFile($file);
                }
            }
            $this->cloudinaryFileService->takeOwnerShip([$request->picture_id], Driver::MORPH_NAME, $driver->id);
        }
        if ($request->has('license_picture_id')) {
            if ($driver->licensePicture()->exists()) {
                if (($driver->licensePicture->id != $request->license_picture_id)) {
                    $file = $driver->licensePicture;
                    $this->cloudinaryFileService->deleteFile($file);
                }
            }
            $this->cloudinaryFileService->takeOwnerShip([$request->license_picture_id], Driver::MORPH_NAME, $driver->id);
        }
        $driver->update($request->validated());
        return $this->respondSuccess([], 'Driver updated successfully');
    }

    public function changeDriverPicture(DriverRequest $request, Driver $driver)
    {
        $this->authorize('update', $driver);
        if ($request->has('picture_id')) {
            if ($driver->picture()->exists()) {
                if (($driver->picture->id != $request->picture_id)) {
                    $file = $driver->picture;
                    $this->cloudinaryFileService->deleteFile($file);
                }
            }
            $driver->picture_id = $request->picture_id;
            $driver->save();
            $this->cloudinaryFileService->takeOwnerShip([$request->picture_id], Driver::MORPH_NAME, $driver->id);
        }
        return $this->respondSuccess([], 'Driver picture updated successfully');
    }

    public function changeDriverLicensePicture(DriverRequest $request, Driver $driver)
    {
        $this->authorize('update', $driver);
        if ($request->has('license_picture_id')) {
            if ($driver->licensePicture()->exists()) {
                if (($driver->licensePicture->id != $request->license_picture_id)) {
                    $file = $driver->licensePicture;
                    $this->cloudinaryFileService->deleteFile($file);
                }
            }
            $driver->license_picture_id = $request->license_picture_id;
            $driver->save();
            $this->cloudinaryFileService->takeOwnerShip([$request->license_picture_id], Driver::MORPH_NAME, $driver->id);
        }

        return $this->respondSuccess([], 'Driver license picture updated successfully');
    }

    public function changeDriverOwner(Request $request, Driver $driver)
    {
        $this->authorize('update', $driver);
        $this->validate($request, [
            'transporter_id' => ['required', 'integer', 'exists:users,id'],
        ]);
        $transporterUser = User::query()->findOrFail($request->transporter_id);
        if ($transporterUser->user_type != User::USER_TYPE_TRANSPORTER) {
            return  $this->respondError('The supplied transported id does not belong to a transporter', 403);
        }
        $driver->user_id = $request->transporter_id;
        $driver->save();
        return $this->respondSuccess([], 'Driver owner updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Driver $driver
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Driver $driver): JsonResponse
    {
        $this->authorize('delete', $driver);
        $driver->delete();
        return $this->respondSuccess([],  'Driver deleted');
    }
}
