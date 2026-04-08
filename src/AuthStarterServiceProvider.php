<?php

declare(strict_types=1);

namespace Deixtra\LaravelStarterAuth;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ServiceProvider;
use Deixtra\LaravelStarterAuth\Console\InstallCommand;

class AuthStarterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/auth-starter.php',
            'auth-starter'
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Published views take priority; package views are the fallback.
        $publishedViewsPath = resource_path('views/vendor/auth-starter');

        if (is_dir($publishedViewsPath)) {
            $this->loadViewsFrom($publishedViewsPath, 'auth-starter');
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'auth-starter');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Ensure $errors is always a MessageBag inside every package view.
        view()->composer('auth-starter::*', function ($view): void {
            $data = $view->getData();
            if (! isset($data['errors'])) {
                $view->with('errors', session('errors', new MessageBag()));
            }
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/auth-starter'),
            ], 'auth-starter-views');

            $this->publishes([
                __DIR__ . '/../config/auth-starter.php' => config_path('auth-starter.php'),
            ], 'auth-starter-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'auth-starter-migrations');

            $this->publishes([
                __DIR__ . '/Http/Controllers' => app_path('Http/Controllers'),
            ], 'auth-starter-controllers');

            $this->publishes([
                __DIR__ . '/Http/Middleware' => app_path('Http/Middleware'),
            ], 'auth-starter-middleware');
        }
    }
}
