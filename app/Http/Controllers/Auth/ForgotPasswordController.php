<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Repositories\Eloquent\Repository\UserRepository;
use App\Services\AppTokenService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ForgotPasswordController extends Controller
{

    private AppTokenService $appTokenService;
    private UserService $userService;
    private  UserRepository $userRepository;

    public function __construct(AppTokenService $appTokenService, UserService $userService, UserRepository $userRepository)
    {
        $this->appTokenService = $appTokenService;
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     *  Forgot Password
     *
     *  A token will be sent to user email address
     *
     */
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {

        $email = $request->input('email');
        try {
            /* @var User $user */
            $user = $this->userRepository->findByEmail($email);
            if (!$user) {
                return $this->respondError('The supplied email does not exist');
            }
            $passwordResetToken = $this->appTokenService->generateUserPasswordResetToken($user);
            $this->userService->sendResetPasswordVerificationNotification($user, $passwordResetToken);
            return $this->respondSuccess([], 'Please check your email address for reset token');
        } catch (\Exception  $exception) {
            return $this->respondInternalError($exception->getMessage());
        }
    }


    public function verifyResetPasswordToken(Request $request)
    {
        $this->validate($request, [
            'token' => ['required', 'string', 'min:6'],
            'email' => ['required', 'email']
        ]);
        $token = $request->input('token');
        $email = $request->input('email');
        /* @var User $user */
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return $this->respondError('The supplied email does not exist');
        }
        $this->appTokenService->validatePasswordResetToken($user, $token);

        return $this->respondSuccess([], 'Token is valid');
    }


    /**
     *  Reset Password
     *
     *  User is expected to supply the token sent to his email address or phone number and a new password
     *
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $token = $request->input('token');
        $password = $request->input('password');
        $email = $request->input('email');
        DB::transaction(function () use ($email, $token, $password) {
            /* @var User $user */
            $user = $this->userRepository->findByEmail($email);
            if (!$user) {
                return $this->respondError('The supplied email does not exist');
            }
            $appToken = $this->appTokenService->validatePasswordResetToken($user, $token);
            //prepare password for saving into the database
            $user->password = Hash::make($password);
            //Invalidate Otp
            $this->appTokenService->invalidateAppToken($user, $appToken);
            $user->save();
        });

        return $this->respondSuccess([], 'Success!!, Password has been updated.');
    }
}
