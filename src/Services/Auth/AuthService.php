<?php

namespace Vendor\ApiAuth\Services\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = config("api-auth.user_model");
    }

    public function register(array $data)
    {
        $user = $this->userModel::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (config('api-auth.verify_email')) {
            $user->sendEmailVerificationNotification();
        }

        return $this->generateResponse($user);
    }

    public function login(array $credentials)
    {
        $user = $this->userModel::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return $this->generateResponse($user);
    }

    public function logout($user)
    {
        return $user->currentAccessToken()->delete();
    }

    protected function generateResponse($user)
    {
        return [
            'user'  => $user,
            'token' => $user->createToken(config('api-auth.token_name'))->plainTextToken,
        ];
    }
}