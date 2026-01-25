<?php

namespace Martin6363\ApiAuth\Services\v1;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordService
{
    /**
     * Send password reset link to user.
     *
     * @throws ValidationException
     */
    public function sendResetLink(array $data): string
    {
        $status = Password::broker()->sendResetLink($data);

        if ($status !== Password::RESET_LINK_SENT) {
            Log::warning('Password reset link failed to send', [
                'email' => $data['email'] ?? 'unknown',
                'status' => $status,
            ]);

            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        Log::info('Password reset link sent', ['email' => $data['email']]);

        return __($status);
    }

    /**
     * Reset user password.
     *
     * @throws ValidationException
     */
    public function reset(array $data): string
    {
        $status = Password::broker()->reset($data, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();

            // Revoke all tokens for security
            $user->tokens()->delete();

            Log::info('Password reset successfully', ['user_id' => $user->id]);
        });

        if ($status !== Password::PASSWORD_RESET) {
            Log::warning('Password reset failed', [
                'email' => $data['email'] ?? 'unknown',
                'status' => $status,
            ]);

            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }
}
