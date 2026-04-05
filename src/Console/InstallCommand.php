<?php

declare(strict_types=1);

namespace Deixtra\LaravelStarterAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'auth-starter:install {--force : Overwrite existing files}';

    protected $description = 'Install the Deixtra Laravel Starter Auth package';

    public function handle(): int
    {
        $this->line('');
        $this->info('🚀 Welcome to Deixtra Laravel Starter Auth Installer');
        $this->line('─────────────────────────────────────────────────────');
        $this->line('');

        // QUESTION 1: User model choice
        $userModelChoice = $this->choice(
            'Do you want to use your existing App\\Models\\User model or create a new one?',
            [
                1 => 'Use existing App\\Models\\User (recommended)',
                2 => 'Create a new User model inside the package',
            ],
            1
        );

        $useExistingModel = str_contains($userModelChoice, 'existing');

        // QUESTION 2: Run migrations
        $runMigrations = $this->confirm('Do you want to run migrations now?', true);

        // QUESTION 3: Route prefix
        $routePrefix = $this->ask(
            'What prefix do you want for auth routes? (leave empty for none, e.g. "auth")',
            ''
        );

        $routePrefix = trim((string) $routePrefix, '/');

        $this->line('');
        $this->line('⚙️  Installing Deixtra Laravel Starter Auth...');
        $this->line('');

        // Step 1: Publish views
        $this->callSilent('vendor:publish', [
            '--tag'   => 'auth-starter-views',
            '--force' => $this->option('force'),
        ]);
        $this->line('✅ Views published');

        // Step 2: Publish config
        $this->callSilent('vendor:publish', [
            '--tag'   => 'auth-starter-config',
            '--force' => $this->option('force'),
        ]);
        $this->line('✅ Config published');

        // Step 3: Conditionally create User model
        if (! $useExistingModel) {
            $this->createUserModel();
            $this->line('✅ User model created at app/Models/AuthUser.php');
        }

        // Step 4: Run migrations
        if ($runMigrations) {
            $this->callSilent('vendor:publish', [
                '--tag'   => 'auth-starter-migrations',
                '--force' => $this->option('force'),
            ]);
            $this->call('migrate');
            $this->line('✅ Migrations executed');
        }

        // Step 5: Inject middleware alias into bootstrap/app.php
        $this->injectMiddlewareAlias();
        $this->line('✅ Middleware alias registered');

        // Step 6: Save route prefix into published config
        $this->saveRoutePrefix($routePrefix);
        $this->line('✅ Route prefix configured: ' . ($routePrefix ?: '(none)'));

        // Step 7: Print success summary
        $this->printSuccessSummary($useExistingModel, $runMigrations, $routePrefix);

        return self::SUCCESS;
    }

    protected function createUserModel(): void
    {
        $destination = app_path('Models/AuthUser.php');

        if (File::exists($destination) && ! $this->option('force')) {
            $this->warn('⚠️  AuthUser model already exists. Use --force to overwrite.');
            return;
        }

        $stub = <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AuthUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}
PHP;

        File::ensureDirectoryExists(app_path('Models'));
        File::put($destination, $stub);
    }

    protected function injectMiddlewareAlias(): void
    {
        $bootstrapPath = base_path('bootstrap/app.php');

        if (! File::exists($bootstrapPath)) {
            $this->warn('⚠️  bootstrap/app.php not found. Please add the middleware alias manually.');
            return;
        }

        $content = File::get($bootstrapPath);
        $middlewareClass = 'Deixtra\\LaravelStarterAuth\\Http\\Middleware\\RedirectIfAuthenticated';
        $alias = "'auth.starter'";

        if (str_contains($content, 'auth.starter')) {
            return;
        }

        // Look for withMiddleware callback and inject alias
        $injection = "\n        \$middleware->alias([\n            'auth.starter' => \\" . $middlewareClass . "::class,\n        ]);";

        if (str_contains($content, '->withMiddleware(')) {
            // Try to inject into existing withMiddleware block
            $content = preg_replace(
                '/(->withMiddleware\(function\s*\(Middleware\s+\$middleware\)\s*\{)/',
                '$1' . $injection,
                $content,
                1
            );

            if ($content !== null) {
                File::put($bootstrapPath, $content);
                return;
            }
        }

        $this->warn('⚠️  Could not auto-inject middleware. Add manually to bootstrap/app.php:');
        $this->line("    \$middleware->alias(['auth.starter' => \\" . $middlewareClass . "::class]);");
    }

    protected function saveRoutePrefix(string $prefix): void
    {
        $configPath = config_path('auth-starter.php');

        if (! File::exists($configPath)) {
            return;
        }

        $content = File::get($configPath);
        $escaped = addslashes($prefix);

        $content = preg_replace(
            "/('route_prefix'\s*=>\s*env\('AUTH_STARTER_PREFIX',\s*'[^']*'\))/",
            "'route_prefix' => '" . $escaped . "'",
            $content
        );

        if ($content !== null) {
            File::put($configPath, $content);
        }
    }

    protected function printSuccessSummary(bool $useExistingModel, bool $ranMigrations, string $routePrefix): void
    {
        $prefix = $routePrefix ? '/' . $routePrefix : '';

        $this->line('');
        $this->line('─────────────────────────────────────────────────────');
        $this->info('🎉 Deixtra Laravel Starter Auth installed successfully!');
        $this->line('─────────────────────────────────────────────────────');
        $this->line('');
        $this->line('📦 What was installed:');
        $this->line('');
        $this->line("   🖼️  Views     → resources/views/vendor/auth-starter/");
        $this->line("   ⚙️  Config    → config/auth-starter.php");
        $this->line('   🛡️  Middleware → auth.starter alias registered');

        if (! $useExistingModel) {
            $this->line('   👤 User Model → app/Models/AuthUser.php');
        } else {
            $this->line('   👤 User Model → Using existing App\\Models\\User');
        }

        if ($ranMigrations) {
            $this->line('   🗄️  Migrations → Executed successfully');
        }

        $this->line('');
        $this->line('🌐 Available Routes:');
        $this->line('');
        $this->line("   GET  {$prefix}/login              → auth-starter.login");
        $this->line("   POST {$prefix}/login              → (login handler)");
        $this->line("   GET  {$prefix}/register           → auth-starter.register");
        $this->line("   GET  {$prefix}/forgot-password    → auth-starter.password.request");
        $this->line("   GET  {$prefix}/reset-password     → auth-starter.password.reset");
        $this->line("   GET  {$prefix}/dashboard          → auth-starter.dashboard");
        $this->line("   POST {$prefix}/logout             → auth-starter.logout");
        $this->line('');
        $this->line('📖 Documentation: https://github.com/deixtra/laravel-starter-auth');
        $this->line('');
        $this->info('✨ Powered by Deixtra Private Limited — https://deixtra.com');
        $this->line('');
    }
}
