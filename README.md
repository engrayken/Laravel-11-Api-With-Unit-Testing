# Laravel 11 Sanctum API Authentication with Unit Tests

This guide walks you through setting up Laravel 11 with Sanctum authentication for login and registration, along with unit tests.

## Installation

### 1. Install Laravel 11
```bash
composer create-project laravel/laravel sanctum-api
cd sanctum-api
```

### 2. Install Sanctum
```bash
php artisan install:api
```

This command installs Laravel Sanctum and sets up API authentication.

### 3. Configure Sanctum
In `app/Models/User.php`, add the `HasApiTokens` trait:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}
```

## Authentication Controllers

### 4. Create Auth Controller
```bash
php artisan make:controller AuthController
```

In `AuthController.php`, add the following:
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }
}
```

### 5. Define API Routes
Add the following to `routes/api.php`:
```php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
```

## Unit Testing

### 6. Create Auth Test
```bash
php artisan make:test AuthTest
```

Edit `tests/Feature/AuthTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['access_token', 'token_type']);
    }

    public function test_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type']);
    }
}
```

Run the tests:
```bash
php artisan test
```

## Troubleshooting `install:api`

If you encounter issues with `php artisan install:api`, downgrade `symfony/process` to version 7.0 by modifying `composer.json`:
```json
"require": {
    "symfony/process": "7.0.*"
}
```

Then run:
```bash
composer update
```

## Conclusion
You now have a Laravel 11 application with Sanctum authentication and unit tests for login and registration.
