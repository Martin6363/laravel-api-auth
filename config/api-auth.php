<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The User model to be used for authentication. This should be the fully
    | qualified class name of your User model.
    |
    */
    'user_model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Token Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Sanctum tokens.
    |
    */
    'token' => [
        /*
         * The name of the token that will be created using Sanctum.
         */
        'name' => 'auth_token',

        /*
         * Token abilities/permissions. Leave empty for no abilities.
         */
        'abilities' => ['*'],

        /*
         * Token expiration time in minutes. Set to null for no expiration.
         */
        'expires_at' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the API routes for authentication.
    |
    */
    'routes' => [
        /*
         * Route prefix for all authentication endpoints.
         */
        'prefix' => 'api/auth',

        /*
         * Route middleware applied to all authentication routes.
         */
        'middleware' => ['api'],

        /*
         * Enable or disable specific routes.
         */
        'enabled' => [
            'register' => true,
            'login' => true,
            'logout' => true,
            'forgot_password' => true,
            'reset_password' => true,
            'email_verification' => true,
            'profile' => true,
            'refresh_token' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    |
    | Configuration for email verification functionality.
    |
    */
    'email_verification' => [
        /*
         * Require email verification when registering.
         */
        'required' => false,

        /*
         * Send verification email automatically after registration.
         */
        'send_on_register' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for password validation and reset.
    |
    */
    'password' => [
        /*
         * Minimum password length.
         */
        'min_length' => 8,

        /*
         * Require password confirmation on registration/reset.
         */
        'require_confirmation' => true,

        /*
         * Password reset token expiration in minutes.
         */
        'reset_token_expires' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for authentication endpoints.
    |
    */
    'rate_limiting' => [
        /*
         * Enable rate limiting.
         */
        'enabled' => true,

        /*
         * Maximum attempts per minute for login/register.
         */
        'max_attempts' => 5,

        /*
         * Maximum attempts per minute for password reset.
         */
        'password_reset_max_attempts' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Configuration
    |--------------------------------------------------------------------------
    |
    | Configure API response structure.
    |
    */
    'response' => [
        /*
         * Include user data in login/register responses.
         */
        'include_user' => true,

        /*
         * Fields to hide from user responses (e.g., 'password', 'remember_token').
         */
        'hidden_fields' => ['password', 'remember_token'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Customize validation rules for authentication requests.
    | Add any additional fields you want to include in registration/login.
    | These fields will be automatically validated and saved to the database.
    |
    | Example: To add a 'phone' field, add:
    | 'phone' => ['required', 'string', 'max:20'],
    |
    | Note: 'email' and 'password' are handled specially:
    | - 'email' automatically gets 'unique' rule for registration
    | - 'password' gets min_length and confirmation rules automatically
    |
    */
    'validation' => [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255'],
        'password' => ['required', 'string'],
        // Add more fields here as needed:
        // 'phone' => ['nullable', 'string', 'max:20'],
        // 'username' => ['required', 'string', 'max:255', 'unique:users,username'],
        // 'dob' => ['nullable', 'date'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Registration Fields
    |--------------------------------------------------------------------------
    |
    | Fields that should be included during user registration.
    | If empty, all fields from 'validation' (except password) will be used.
    | Password is always handled separately (hashed).
    |
    | Leave empty to use all validation fields automatically.
    |
    */
    'registration_fields' => [],
];