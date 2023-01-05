<?php

namespace App\Services;

use App\Models\AppToken;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AppTokenService
{

    public function generateUserEmailToken(User $user): AppToken
    {
        $emailToken = new AppToken([
            'target_type' => AppToken::TARGET_TYPE_USER,
            'target_id' => $user->id,
            'type' => AppToken::TYPE_EMAIL_VERIFICATION,
            'token' => $this->getToken(),
            'expires_at' => strtotime('+48 hours'),
        ]);
        $emailToken->save();
        return $emailToken;
    }

    public function generateUserPasswordResetToken(User $user): AppToken
    {
        $emailToken = new AppToken([
            'target_type' => AppToken::TARGET_TYPE_USER,
            'target_id' => $user->id,
            'type' => AppToken::TYPE_PASSWORD_RESET,
            'token' => $this->getToken(),
            'expires_at' => strtotime('+48 hours'),
        ]);
        $emailToken->save();
        return $emailToken;
    }

    public function generateUserPhoneNumberToken(User $user): AppToken
    {
        $emailToken = new AppToken([
            'target_type' => AppToken::TARGET_TYPE_USER,
            'target_id' => $user->id,
            'type' => AppToken::TYPE_PHONE_NUMBER_VERIFICATION,
            'token' => $this->getToken(),
            'expires_at' => strtotime('+48 hours'),
        ]);
        $emailToken->save();
        return $emailToken;
    }

    public function validateEmailVerificationToken(User $user, string $token): AppToken
    {
        /** @var AppToken $emailToken */
        $emailToken = AppToken::query()->where([
            ['target_type', AppToken::TARGET_TYPE_USER],
            ['target_id', $user->id],
            ['type', AppToken::TYPE_EMAIL_VERIFICATION],
            ['token', $token],
        ])->first();
        if (!$emailToken || $emailToken->used) throw new BadRequestHttpException('Invalid email verification token');
        if ($emailToken->hasExpired()) throw new BadRequestHttpException('Email verification token expired. Please re-attempt verification.');
        return $emailToken;
    }

    public function validatePasswordResetToken(User $user, string $token): AppToken
    {
        /** @var AppToken $passwordResetToken */
        $passwordResetToken = AppToken::query()->where([
            ['target_type', AppToken::TARGET_TYPE_USER],
            ['target_id', $user->id],
            ['type', AppToken::TYPE_PASSWORD_RESET],
            ['token', $token],
        ])->first();
        if (!$passwordResetToken || $passwordResetToken->used) throw new BadRequestHttpException('Invalid password reset token');
        if ($passwordResetToken->hasExpired()) throw new BadRequestHttpException('Password reset token expired. Please re-attempt forgot password process.');
        return $passwordResetToken;
    }

    public function validatePhoneNumberToken(User $user, string $token): AppToken
    {
        /** @var AppToken $emailToken */
        $emailToken = AppToken::query()->where([
            ['target_type', AppToken::TARGET_TYPE_USER],
            ['target_id', $user->id],
            ['type', AppToken::TYPE_PHONE_NUMBER_VERIFICATION],
            ['token', $token],
        ])->first();
        if (!$emailToken || $emailToken->used) throw new BadRequestHttpException('Invalid password reset token');
        if ($emailToken->hasExpired()) throw new BadRequestHttpException('Password reset token expired. Please re-attempt verification.');
        return $emailToken;
    }

    public function invalidateAppToken(User $user, AppToken $appToken): void
    {
        AppToken::query()->where([
            ['target_type', $appToken->target_type],
            ['target_id', $user->id],
            ['type', $appToken->type],
            ['used', false],
            ['active', true],
        ])->update(array('active' => false, 'used' => true));
    }

    private function getToken(): string
    {
        return (string)rand(100000, 999999);
    }
}
