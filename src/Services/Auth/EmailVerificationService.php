<?php

namespace Vendor\ApiAuth\Services\Auth;

use Illuminate\Validation\ValidationException;

class EmailVerificationService
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = config("api-auth.user_model");
    }

    public function sendNotification($user)
    {
        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages(['email' => [__('auth.already_verified')]]);
        }

        $user->sendEmailVerificationNotification();

        return __('auth.verification_sent');
    }

    public function verify($userId, $hash)
    {
        $user = $this->userModel::findOrFail($userId);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw ValidationException::withMessages(['url' => [__('auth.invalid_verify_url')]]);
        }

        if ($user->hasVerifiedEmail()) {
            return __('auth.already_verified');
        }

        $user->markEmailAsVerified();

        return __('auth.email_verified');
    }
}