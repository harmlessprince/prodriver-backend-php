<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AppTokenService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VerificationController extends Controller
{
    public function __construct(private readonly AppTokenService $appTokenService)
    {
    }

    public function verify(Request $request, $token): JsonResponse
    {
        /* @var  User $user */
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            throw new  BadRequestHttpException('Email has already been verified');
        }
        //check if supplied token is valid
        $emailToken = $this->appTokenService->validateEmailVerificationToken($user, $token);
        //mark email as verified
        $user->markEmailAsVerified();
        //mark otp as used
        $this->appTokenService->invalidateAppToken($user, $emailToken);
        //CRON JOB to delete otp's
        //return response
        return $this->respondSuccess([], 'Email verification successful');
    }

    public function resend(Request $request, UserService $userService): JsonResponse
    {
        /* @var  User $user */
        $user = $request->user();
        //check if email has already been verified
        if ($user->hasVerifiedEmail()) {
            throw new  BadRequestHttpException('Email has already been verified');
        }
        //resend email verification
        $emailToken = $this->appTokenService->generateUserEmailToken($user);
        $userService->sendEmailVerificationNotification($user, $emailToken->token);
        //return response
        return $this->respondSuccess([], 'Email verification code sent successfully');
    }
}
