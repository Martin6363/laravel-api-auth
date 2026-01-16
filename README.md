# Laravel API Auth

A professional, configuration-driven API authentication package for Laravel 12+ using Laravel Sanctum. Built with clean architecture principles and designed for easy customization.

## ✨ Features
- ✅ **Full API Auth Flow**: Login, Registration, Logout.
- ✅ **Secure Password Management**: Forgot Password & Reset Password.
- ✅ **Email Verification**: Built-in API support for verifying users.
- ✅ **Clean Architecture**: Business logic is decoupled into Service classes.
- ✅ **Highly Extensible**: Easily override controllers, requests, or services.
- ✅ **Test Driven**: Ships with a complete Pest test suite.
>>>>>>> 572ac741b9045bf5f06042e50fb79fb8bd26e070

- 🔐 **Complete Authentication Flow**: Registration, Login, Logout
- 🔑 **Token Management**: Access tokens with refresh capability
- 🔒 **Password Management**: Forgot password and reset password functionality
- ✉️ **Email Verification**: Built-in email verification support
- 👤 **User Profile**: Get authenticated user profile endpoint
- 🛡️ **Rate Limiting**: Configurable rate limiting for security
- ⚙️ **Highly Configurable**: Extensive configuration options
- 🏗️ **Clean Architecture**: Service-oriented design for easy extension
- 📝 **Well Documented**: Comprehensive documentation and code comments
- 🧪 **Test Ready**: Built with testing in mind

## 📋 Requirements

- PHP >= 8.2
- Laravel >= 11.0 or >= 12.0
- Laravel Sanctum >= 4.0

## 🚀 Installation

### Step 1: Install via Composer

```bash
composer require Martin6363/laravel-api-auth
```

<<<<<<< HEAD
### Step 2: Install Laravel Sanctum (if not already installed)

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### Step 3: Install the Package

=======
1. Install filles in your project
- ✅ **Publish the configuration file.**
- ✅ **Publish the API routes.**
- ✅ **Publish the controllers, requests, and services.**
```bash
php artisan api-auth:install
```

<<<<<<< HEAD
This command will:
- Publish the configuration file to `config/api-auth.php`
- Check for Laravel Sanctum installation
- Optionally run migrations

### Step 4: Configure Your User Model

Ensure your `User` model uses the `HasApiTokens` trait from Laravel Sanctum:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // ...
}
```

### Step 5: Configure Email (Optional)

If you're using email verification or password reset, configure your email settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## ⚙️ Configuration

After installation, you can customize the package behavior by editing `config/api-auth.php`:

### User Model

```php
'user_model' => \App\Models\User::class,
```

### Token Configuration

```php
'token' => [
    'name' => 'auth_token',           // Token name
    'abilities' => ['*'],             // Token abilities
    'expires_at' => null,            // Token expiration (null = no expiration)
],
```

### Route Configuration

```php
'routes' => [
    'prefix' => 'api/auth',           // Route prefix
    'middleware' => ['api'],          // Route middleware
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
```

### Email Verification

```php
'email_verification' => [
    'required' => false,              // Require verification on registration
    'send_on_register' => true,       // Auto-send verification email
],
```

### Password Configuration

```php
'password' => [
    'min_length' => 8,                // Minimum password length
    'require_confirmation' => true,    // Require password confirmation
    'reset_token_expires' => 60,       // Reset token expiration (minutes)
],
```

### Rate Limiting

```php
'rate_limiting' => [
    'enabled' => true,                 // Enable rate limiting
    'max_attempts' => 5,               // Max attempts per minute (login/register)
    'password_reset_max_attempts' => 3, // Max attempts for password reset
],
```

## 📡 API Endpoints

### Public Endpoints

#### Register
```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "message": "User registered successfully",
    "data": {
        "token": "1|xxxxxxxxxxxx",
        "token_type": "Bearer",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "data": {
        "token": "1|xxxxxxxxxxxx",
        "token_type": "Bearer",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}
```

#### Forgot Password
```http
POST /api/auth/forgot-password
Content-Type: application/json

{
    "email": "john@example.com"
}
```

#### Reset Password
```http
POST /api/auth/reset-password
Content-Type: application/json

{
    "token": "reset_token_here",
    "email": "john@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

### Protected Endpoints (Require Authentication)

All protected endpoints require the `Authorization` header:
```http
Authorization: Bearer {token}
```

#### Get Profile
```http
GET /api/auth/profile
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Profile retrieved successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

#### Refresh Token
```http
POST /api/auth/refresh-token
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Token refreshed successfully",
    "data": {
        "token": "2|xxxxxxxxxxxx",
        "token_type": "Bearer",
        "user": {  }
    }
}
```

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Logged out successfully"
}
```

#### Send Email Verification
```http
POST /api/auth/email/verification-notification
Authorization: Bearer {token}
```

#### Verify Email
```http
GET /api/auth/email/verify/{id}/{hash}
```

## 🔧 Customization

### Adding Custom Fields to Registration

You can easily add custom fields to the registration process by adding them to the validation configuration. The package will automatically:

1. Validate the fields during registration
2. Save them to the database
3. Include them in API responses

**Example: Adding a phone number field**

1. Update your `config/api-auth.php`:

```php
'validation' => [
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255'],
    'password' => ['required', 'string'],
    'phone' => ['nullable', 'string', 'max:20'], // Add your custom field
    'username' => ['required', 'string', 'max:255', 'unique:users,username'],
],
```

2. Make sure your User model's `$fillable` array includes the new field:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'phone',      // Add your custom field
    'username',   // Add your custom field
];
```

3. The field will now be automatically:
   - Validated during registration
   - Saved to the database
   - Included in API responses

**Example Registration Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+1234567890",
    "username": "johndoe"
}
```

### Customizing Validation Rules

Edit `config/api-auth.php`:

```php
'validation' => [
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255'],
    'password' => ['required', 'string', 'min:12'], // Customize password rules
],
```

**Note:** 
- The `email` field automatically gets a `unique` rule for registration
- The `password` field automatically gets `min_length` and `confirmed` rules based on config
- All other fields use the rules you specify exactly as configured

### Extending Services

You can extend the services by binding your own implementations in a service provider:

```php
use Vendor\ApiAuth\Services\Auth\AuthService;

$this->app->bind(AuthService::class, function ($app) {
    return new CustomAuthService();
});
```

### Customizing Routes

You can disable specific routes in the configuration:

```php
'routes' => [
    'enabled' => [
        'register' => false,  // Disable registration
        'login' => true,
        // ...
    ],
],
```

Or modify the route prefix:

```php
'routes' => [
    'prefix' => 'api/v1/auth',  // Custom prefix
],
```

## 🛡️ Security Features

- **Rate Limiting**: Prevents brute force attacks
- **Password Hashing**: Uses Laravel's secure password hashing
- **Token Revocation**: Tokens are revoked on password reset
- **Email Verification**: Optional email verification for new users
- **CSRF Protection**: Built-in CSRF protection for web routes

## 🧪 Testing

The package includes test examples. Run tests with:

```bash
php artisan test
```

Or with Pest:

```bash
./vendor/bin/pest
```

## 📝 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📧 Support

For issues, questions, or contributions, please open an issue on the GitHub repository.

## 🙏 Credits

Built with ❤️ for the Laravel community.
