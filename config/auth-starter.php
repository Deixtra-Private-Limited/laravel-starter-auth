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
    | Published File Names
    |--------------------------------------------------------------------------
    | These are set automatically by the installer and track what names the
    | user chose for their published controller, middleware, routes file, and
    | views directory.
    */
    'controller_name' => 'AuthController',
    'middleware_name'  => 'AuthMiddleware',
    'routes_file'      => 'auth',
    'views_path'       => 'auth-starter',

    /*
    |--------------------------------------------------------------------------
    | View Overrides
    |--------------------------------------------------------------------------
    | Customize which views are used for each auth page.
    */
    'login_view'            => 'auth.login',
    'register_view'         => 'auth.register',
    'forgot_password_view'  => 'auth.forgot-password',
    'reset_password_view'   => 'auth.reset-password',
    'dashboard_view'        => 'dashboard',

];
