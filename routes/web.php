<?php

declare(strict_types=1);

use Deixtra\LaravelStarterAuth\Http\Controllers\AuthController;
use Deixtra\LaravelStarterAuth\Http\Controllers\DashboardController;
use Deixtra\LaravelStarterAuth\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

$prefix = config('auth-starter.route_prefix', '');
$registrationEnabled = config('auth-starter.registration_enabled', true);

// Auth routes with prefix (login, register, password reset)
Route::prefix($prefix)
    ->name('auth-starter.')
    ->group(function () use ($registrationEnabled): void {

        Route::middleware('guest')->group(function () use ($registrationEnabled): void {

            Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('/login', [AuthController::class, 'login']);

            if ($registrationEnabled) {
                Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
                Route::post('/register', [AuthController::class, 'register']);
            }

            Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
            Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
            Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
            Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
        });
    });

// Dashboard & logout - no prefix
Route::middleware('auth')
    ->name('auth-starter.')
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });