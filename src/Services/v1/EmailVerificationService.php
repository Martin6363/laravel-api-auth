<?php

namespace Martin6363\ApiAuth\Services\v1;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Martin6363\ApiAuth\Notifications\EmailVerificationNotification;

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
     * @param  Authenticatable  $user
     *
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
            $url = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(Config::get('api-auth.email_verification.expire', 60)),
                ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
            );

            $user->notify(new EmailVerificationNotification($url));

            Log::info('Verification email processed', [
                'user_id' => $user->id,
                'mode' => Config::get('api-auth.emails.dispatch_mode'),
            ]);

            return __('auth.verification_sent');
        } catch (\Exception $e) {
            Log::error('Verify send Mail error: '.$e->getMessage());

            return __('auth.verification_send_failed');
        }
    }

    /**
     * Verify user email.
     *
     * @param  int|string  $userId
     *
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
