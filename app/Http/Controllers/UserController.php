<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
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
        $this->authorize('viewAny', User::class);
        $userTypes = User::ALL_USER_TYPES;
        if (!in_array($request->query('user_type'), $userTypes)) {
            return $this->respondError('Please provide a valid user type in params');
        }
        $users = User::query()->select('id', 'first_name', 'middle_name', 'last_name', 'phone_number', 'user_type')->where('user_type', $request->query('user_type'))->get();
        return $this->respondSuccess(['users' => $users], 'User fetched successfully');
    }

    /**
     * @throws AuthorizationException
     */
    public function getAllTransporters(): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        $users = User::query()->select('id', 'first_name', 'middle_name', 'last_name', 'phone_number', 'user_type', 'email')->where('user_type', User::USER_TYPE_TRANSPORTER)->get();
        return $this->respondSuccess(['users' => $users], 'User fetched successfully');
    }

    /**
     * @throws AuthorizationException
     */
    public function getAllCargoOwners(): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        $users = User::query()->select('id', 'first_name', 'middle_name', 'last_name', 'phone_number', 'user_type', 'email')->where('user_type', User::USER_TYPE_CARGO_OWNER)->get();
        return $this->respondSuccess(['users' => $users], 'User fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateUserRequest $request
     * @param UserService $userService
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Exception
     */
    public function store(CreateUserRequest $request, UserService $userService): JsonResponse
    {
        $this->authorize('create', User::class);
        $user = $userService->createUser((object)$request->validated());
        return $this->respondSuccess(['user' => $user], 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);
        $user = $user->load($user->myRelations($user->user_type));
        return $this->respondSuccess(['user' => $user], 'User fetched successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CreateUserRequest $request
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(CreateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        $user->update($request->validated());
        return $this->respondSuccess([], 'User profile updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Response
     */
    public function destroy(User $user)
    {

    }
}
