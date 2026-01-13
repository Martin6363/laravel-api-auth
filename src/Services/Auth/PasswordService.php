<?php

namespace Vendor\ApiAuth\Services\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordService
{
    public function sendResetLink(array $data)
    {
        $status = Password::broker()->sendResetLink($data);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['email' => [__($status)]]);
        }

        return __($status);
    }

    public function reset(array $data)
    {
        $status = Password::broker()->reset($data, function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => [__($status)]]);
        }

        return __($status);
    }
}