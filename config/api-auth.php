<?php

return [
    /*
     * The User model to be used for Auth.
     */
    'user_model' => \App\Models\User::class,

    /*
     * The name of the token that will be created using Sanctum.
     */
    'token_name' => 'auth_token',

    /*
     * Is Email Verification required when registering?
     */
    'verify_email' => false,
];