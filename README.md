# deixtra/laravel-starter-auth

[![License: MIT](https://img.shields.io/badge/License-MIT-purple.svg)](https://opensource.org/licenses/MIT)
[![Packagist Version](https://img.shields.io/packagist/v/deixtra/laravel-starter-auth.svg)](https://packagist.org/packages/deixtra/laravel-starter-auth)
[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-blue.svg)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)

A production-ready Laravel 13 authentication scaffold with beautiful Tailwind CSS views, full password reset flow, middleware, migrations, and an interactive installer command.
[![Latest Version](https://img.shields.io/packagist/v/deixtra/laravel-starter-auth.svg)](https://packagist.org/packages/deixtra/laravel-starter-auth)
[![Total Downloads](https://img.shields.io/packagist/dt/deixtra/laravel-starter-auth.svg)](https://packagist.org/packages/deixtra/laravel-starter-auth)
[![License](https://img.shields.io/packagist/l/deixtra/laravel-starter-auth.svg)](https://packagist.org/packages/deixtra/laravel-starter-auth)
---

## Features

- **Interactive installer** — `php artisan auth-starter:install` guides you through setup
- **Full auth flow** — Login, Register, Logout, Forgot Password, Reset Password
- **Beautiful views** — Tailwind CSS with a deep purple (`#50016e`) color scheme
- **Flexible user model** — Use your existing `App\Models\User` or scaffold a new one
- **Route prefix support** — Optionally namespace all routes under `/auth/*`
- **Middleware included** — `RedirectIfAuthenticated` with multi-guard support
- **Zero config required** — Works out of the box with sensible defaults

---

## Requirements

| Dependency       | Version  |
|-----------------|----------|
| PHP             | ^8.3     |
| Laravel         | ^13.0    |

---

## Installation

```bash
composer require deixtra/laravel-starter-auth
```

The service provider is auto-discovered by Laravel's package discovery — no manual registration needed.

---

## Setup

Run the interactive installer:

```bash
php artisan auth-starter:install
```

The installer will ask you:

1. **User model** — Use your existing `App\Models\User` or create a new one
2. **Migrations** — Run migrations immediately or skip
3. **Route prefix** — Optional prefix for all auth routes (e.g. `auth`)

With `--force` to overwrite already-published files:

```bash
php artisan auth-starter:install --force
```

---

## Configuration

After installation, edit `config/auth-starter.php`:

```php
return [
    // URL prefix for all auth routes. '' means /login, 'auth' means /auth/login
    'route_prefix' => env('AUTH_STARTER_PREFIX', ''),

    // Where to redirect after successful login/registration
    'home' => '/dashboard',

    // Set to false to disable registration entirely
    'registration_enabled' => true,

    // true = use App\Models\User, false = use package's own User model
    'use_existing_user_model' => true,

    // Override the login/register view paths
    'login_view'    => 'auth-starter::auth.login',
    'register_view' => 'auth-starter::auth.register',
];
```

You can also set the route prefix via `.env`:

```env
AUTH_STARTER_PREFIX=auth
```

---

## Routes

All routes are prefixed with `auth-starter.` for named route access.

| Method | URI                        | Name                            | Middleware |
|--------|----------------------------|---------------------------------|------------|
| GET    | `/login`                   | `auth-starter.login`            | guest      |
| POST   | `/login`                   | —                               | guest      |
| GET    | `/register`                | `auth-starter.register`         | guest      |
| POST   | `/register`                | —                               | guest      |
| GET    | `/forgot-password`         | `auth-starter.password.request` | guest      |
| POST   | `/forgot-password`         | `auth-starter.password.email`   | guest      |
| GET    | `/reset-password/{token}`  | `auth-starter.password.reset`   | guest      |
| POST   | `/reset-password`          | `auth-starter.password.update`  | guest      |
| GET    | `/dashboard`               | `auth-starter.dashboard`        | auth       |
| POST   | `/logout`                  | `auth-starter.logout`           | auth       |

> If you configured a route prefix (e.g. `auth`), all URIs above become `/auth/login`, `/auth/dashboard`, etc.

Use named routes in your Blade views:

```blade
<a href="{{ route('auth-starter.login') }}">Login</a>
<a href="{{ route('auth-starter.dashboard') }}">Dashboard</a>
```

---

## Middleware

The package registers a `RedirectIfAuthenticated` middleware. The installer auto-injects the alias `auth.starter` into `bootstrap/app.php`.

If auto-injection fails, add it manually:

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'auth.starter' => \Deixtra\LaravelStarterAuth\Http\Middleware\RedirectIfAuthenticated::class,
    ]);
})
```

---

## Customizing Views

Publish the views to your application:

```bash
php artisan vendor:publish --tag=auth-starter-views
```

Views are published to `resources/views/vendor/auth-starter/`. Edit them freely — the package will use your published copies over the package defaults.

### View structure

```
resources/views/vendor/auth-starter/
├── layouts/
│   └── app.blade.php          # Base layout with Tailwind CDN
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── forgot-password.blade.php
│   └── reset-password.blade.php
└── dashboard.blade.php
```

---

## Publishing Individual Assets

```bash
# Publish config only
php artisan vendor:publish --tag=auth-starter-config

# Publish migrations only
php artisan vendor:publish --tag=auth-starter-migrations

# Publish views only
php artisan vendor:publish --tag=auth-starter-views
```

---

## Using a Custom User Model

Set `use_existing_user_model` to `false` in the config and the package will use `Deixtra\LaravelStarterAuth\Models\User`. You can also extend this model in your own application.

---

## License

MIT License — see [LICENSE](LICENSE) for details.

---

## Credits

Built with ❤️ by [Deixtra Private Limited](https://deixtra.com)

- Email: support@deixtra.com
- Website: https://deixtra.com
- GitHub: https://github.com/deixtra/laravel-starter-auth
