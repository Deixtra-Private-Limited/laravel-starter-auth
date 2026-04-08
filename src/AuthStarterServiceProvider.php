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

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'auth-starter');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

      
      

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
