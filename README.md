# Laravel Professional API Auth

A clean, service-oriented authentication package for Laravel 12 using Sanctum.

## Features

- ✅ **Full API Auth Flow**: Login, Registration, Logout.
- ✅ **Secure Password Management**: Forgot Password & Reset Password.
- ✅ **Email Verification**: Built-in API support for verifying users.
- ✅ **Clean Architecture**: Business logic is decoupled into Service classes.
- ✅ **Highly Extensible**: Easily override controllers, requests, or services.
- ✅ **Test Driven**: Ships with a complete Pest test suite.

## Installation

1. Install the package via composer:
```bash
composer require Martin6363/laravel-api-auth
```

2. Install filles in your project
- ✅ **Publish the configuration file.**
- ✅ **Publish the API routes.**
- ✅ **Publish the controllers, requests, and services.**
```bash
php artisan api-auth:install
```

## _POST /api/auth/login_
`Content-Type: application/json`

`{
"email": "user@example.com",
"password": "password123"
}`
