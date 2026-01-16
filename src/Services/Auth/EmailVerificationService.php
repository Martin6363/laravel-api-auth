<?php

namespace Vendor\ApiAuth\Services\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EmailVerificationService
{
    /**
     * The user model class name.
     */
    protected string $userModel;

    /**
     * Create a new EmailVerificationService instance.
     */
    public function __construct()
    {
        $this->userModel = config('api-auth.user_model');
    }

    /**
     * Send email verification notification.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return string
     * @throws ValidationException
     */
    public function sendNotification($user): string
    {
        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => [__('auth.already_verified')],
            ]);
        }

        try {
            $user->sendEmailVerificationNotification();
            
            Log::info('Verification email sent', ['user_id' => $user->id]);

            return __('auth.verification_sent');
        } catch (\Exception $e) {
            Log::error('Failed to send verification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'email' => [__('auth.verification_failed')],
            ]);
        }
    }

    /**
     * Verify user email.
     *
     * @param  int|string  $userId
     * @param  string  $hash
     * @return string
     * @throws ValidationException
     */
    public function verify($userId, string $hash): string
    {
        $user = $this->userModel::findOrFail($userId);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            Log::warning('Invalid verification hash', ['user_id' => $userId]);

            throw ValidationException::withMessages([
                'url' => [__('auth.invalid_verify_url')],
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return __('auth.already_verified');
        }

        $user->markEmailAsVerified();

        Log::info('Email verified successfully', ['user_id' => $user->id]);

        return __('auth.email_verified');
    }
}