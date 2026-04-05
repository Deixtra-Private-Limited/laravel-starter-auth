<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    | Prefix applied to all auth routes. Leave empty for no prefix.
    | Example: 'auth' → /auth/login, /auth/register, /auth/dashboard
    */
    'route_prefix' => env('AUTH_STARTER_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Home Redirect
    |--------------------------------------------------------------------------
    | Where authenticated users are redirected after login or registration.
    */
    'home' => '/dashboard',

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    | Set to false to disable the registration routes and links.
    */
    'registration_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    | When true, uses App\Models\User (your existing model).
    | When false, uses Deixtra\LaravelStarterAuth\Models\User.
    */
    'use_existing_user_model' => true,

    /*
    |--------------------------------------------------------------------------
    | View Overrides
    |--------------------------------------------------------------------------
    | Customize which views are used for login and register pages.
    */
    'login_view'    => 'auth-starter::auth.login',
    'register_view' => 'auth-starter::auth.register',

];
