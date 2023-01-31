<?php

namespace App\Services;

use App\Models\AppToken;
use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\WelcomeToProDriverNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class UserService
{
    /**
     * @throws \Exception
     */
    public function createUser($data): User
    {

        $user = new User();
        $user->first_name = $data->first_name;
        $user->last_name = $data->last_name;
        $user->email = $data->email;
        $user->password = strlen($data->password ?? '') > 0  ?  Hash::make($data->password) : Hash::make(random_bytes(8));
        $user->phone_number = $data->phone_number;
        $user->user_type = $data->user_type;
        $user->save();
        return $user;
    }

    public function sendEmailVerificationNotification(User $user, string $appToken): void
    {
        Notification::send($user, new VerifyEmailNotification($appToken));
    }

    public function sendWelcomeNotification(User $user): void
    {
        //        Notification::send($user, new ());
    }

    public function sendPhoneNumberVerificationNotification(User $user, AppToken $appToken): void
    {
        //TODO
    }

    public function sendResetPasswordVerificationNotification(User $user, AppToken $appToken): void
    {
        Notification::send($user, new ForgotPasswordNotification($appToken->token));
    }
}
