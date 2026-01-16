<?php

namespace Vendor\ApiAuth\Services\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    /**
     * The user model class name.
     */
    protected string $userModel;

    /**
     * Create a new AuthService instance.
     */
    public function __construct()
    {
        $this->userModel = config('api-auth.user_model');
    }

    /**
     * Register a new user.
     *
     * @param  array  $data
     * @return array
     */
    public function register(array $data): array
    {
        // Get fields to include in registration from config
        $registrationFields = config('api-auth.registration_fields', []);
        
        // If registration_fields is empty, use all validation fields except password
        if (empty($registrationFields)) {
            $validationFields = array_keys(config('api-auth.validation', []));
            $registrationFields = array_filter($validationFields, function($field) {
                return $field !== 'password' && $field !== 'password_confirmation';
            });
        }
        
        // Prepare user data - include all fields from validation config that exist in data
        $userData = [];
        
        // Process each field from registration fields
        foreach ($registrationFields as $field) {
            if (isset($data[$field])) {
                $userData[$field] = $data[$field];
            }
        }
        
        // Include any additional fields from validated data that aren't in config
        // This allows for dynamic fields not explicitly listed in validation config
        foreach ($data as $key => $value) {
            // Skip password fields and fields already processed
            if (!in_array($key, ['password', 'password_confirmation']) && !isset($userData[$key])) {
                $userData[$key] = $value;
            }
        }
        
        // Hash password separately (always required)
        if (isset($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user = $this->userModel::create($userData);

        // Send email verification if enabled
        if (config('api-auth.email_verification.send_on_register', false)) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Exception $e) {
                Log::warning('Failed to send verification email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('User registered successfully', ['user_id' => $user->id]);

        return $this->generateResponse($user);
    }

    /**
     * Authenticate a user and return token.
     *
     * @param  array  $credentials
     * @return array
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = $this->userModel::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            Log::warning('Failed login attempt', ['email' => $credentials['email']]);
            
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        Log::info('User logged in successfully', ['user_id' => $user->id]);

        return $this->generateResponse($user);
    }

    /**
     * Logout the authenticated user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return bool
     */
    public function logout($user): bool
    {
        $deleted = $user->currentAccessToken()->delete();
        
        if ($deleted) {
            Log::info('User logged out successfully', ['user_id' => $user->id]);
        }

        return $deleted;
    }

    /**
     * Refresh the user's access token.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return array
     */
    public function refreshToken($user): array
    {
        // Delete current token
        $user->currentAccessToken()->delete();

        // Generate new token
        return $this->generateResponse($user);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return array
     */
    public function getProfile($user): array
    {
        $userData = $user->toArray();
        
        // Hide sensitive fields
        $hiddenFields = config('api-auth.response.hidden_fields', []);
        foreach ($hiddenFields as $field) {
            unset($userData[$field]);
        }

        return ['user' => $userData];
    }

    /**
     * Generate authentication response with user and token.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return array
     */
    protected function generateResponse($user): array
    {
        $tokenConfig = config('api-auth.token', []);
        $tokenName = $tokenConfig['name'] ?? 'auth_token';
        $abilities = $tokenConfig['abilities'] ?? ['*'];
        $expiresAt = $tokenConfig['expires_at'] ?? null;

        // Create token with Sanctum
        $token = $user->createToken($tokenName, $abilities);

        // Set expiration if configured
        if ($expiresAt !== null) {
            $token->accessToken->expires_at = now()->addMinutes($expiresAt);
            $token->accessToken->save();
        }

        $response = [
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ];

        if (config('api-auth.response.include_user', true)) {
            $userData = $user->toArray();
            
            // Hide sensitive fields
            $hiddenFields = config('api-auth.response.hidden_fields', []);
            foreach ($hiddenFields as $field) {
                unset($userData[$field]);
            }

            $response['user'] = $userData;
        }

        return $response;
    }
}