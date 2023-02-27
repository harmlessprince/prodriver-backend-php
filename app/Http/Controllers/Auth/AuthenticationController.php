<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserRegisteredEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Eloquent\Repository\UserRepository;
use App\Services\AppTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{

    public function __construct(private readonly UserRepository $userRepository, private readonly AppTokenService $appTokenService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        /** @var User $user */
        $data = $request->validated();
        unset($data['confirm_password']);
        unset($data['company_name']);
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);
        if ($user->user_type === User::USER_TYPE_CARGO_OWNER) {
            $user->company()->create([
                'name' => $request->company_name,
            ]);
        }
        $user = $user->load('company');
        $emailToken = $this->appTokenService->generateUserEmailToken($user);
        event(new UserRegisteredEvent($user, $emailToken->token));
        $token =  $user->createToken('register-token')->plainTextToken;
        return $this->respondSuccess(['user' => new UserResource($user), 'token' => $token], 'Registration successful');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        /** @var  User $user */
        $user = $this->userRepository->findByEmail($request->email);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->respondError('Provided credentials is invalid', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $token =  $user->createToken('login-token')->plainTextToken;
        $relations = $user->myRelations($user->user_type);
        $user = $user->load($relations);
        return $this->respondSuccess(['user' => new UserResource($user), 'token' => $token], 'Login successful');
    }
    public function user(Request $request): JsonResponse
    {
        /** @var  User $user */
        $user = $request->user();
        $relations = $user->myRelations($user->user_type);
        $user = $user->load($relations);
        return $this->respondWithResource(new UserResource($user), 'User Profile fetched successfully');
    }
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return $this->respondSuccess([], 'Successfully logged out');
    }
}
