<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    /*
    |--------------------------------------------------------------------------
    | API Authentication Messages
    |--------------------------------------------------------------------------
    |
    | Custom messages for API authentication endpoints.
    |
    */

    'registered' => 'User registered successfully.',
    'login_success' => 'Login successful.',
    'logout_success' => 'Logged out successfully.',
    'token_refreshed' => 'Token refreshed successfully.',
    'profile_retrieved' => 'Profile retrieved successfully.',
    'password_reset_link_sent' => 'Password reset link sent successfully.',
    'password_reset_success' => 'Password has been reset successfully.',
    'invalid_token' => 'The provided token is invalid or has expired.',
    'user_not_found' => 'User not found.',
    'invalid_credentials' => 'Invalid credentials provided.',
    'account_locked' => 'Your account has been locked. Please contact support.',
    'account_inactive' => 'Your account is inactive. Please verify your email.',
    'registration_disabled' => 'User registration is currently disabled.',
    'passwords_do_not_match' => 'The provided passwords do not match.',
    'password_too_weak' => 'The provided password does not meet the security requirements.',
    'email_not_found' => 'No user found with the provided email address.',

    /*
    |--------------------------------------------------------------------------
    | Email Verification Messages
    |--------------------------------------------------------------------------
    |
    | Messages related to email verification.
    |
    */

    'verification_sent' => 'Verification email sent successfully.',
    'email_verified' => 'Email verified successfully.',
    'already_verified' => 'Email is already verified.',
    'verification_failed' => 'Failed to send verification email.',
    'invalid_verify_url' => 'Invalid verification URL.',
    'verification_send_failed' => 'Failed to send verification email. Please try again later.',
    'verification_token_expired' => 'Verification token has expired.',
    'verification_email_resent' => 'Verification email resent successfully.',
    'invalid_verification_token' => 'Invalid verification token.',
    'email_already_verified' => 'Email is already verified.',

    /*-------------------------------------------------------------------------
    | Email Verification Notification Lines
    |--------------------------------------------------------------------------
    */
    'verify_email_subject' => 'Verify Your Email Address',
    'verify_email_greeting' => 'Hello!',
    'verify_email_line_1' => 'Please click the button below to verify your email address.',
    'verify_email_line_2' => 'This link will expire in 60 minutes.',
    'verify_email_action' => 'Verify Email',
    /*
    |--------------------------------------------------------------------------
    | Password Reset Messages
    |--------------------------------------------------------------------------
    |
    | Note: Password reset messages are handled by Laravel's built-in
    | password reset system. These are included for reference.
    |
    */
];
