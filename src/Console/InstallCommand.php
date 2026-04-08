<?php

declare(strict_types=1);

namespace Deixtra\LaravelStarterAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'auth-starter:install {--force : Overwrite existing files}';

    protected $description = 'Install the Deixtra Laravel Starter Auth package';

    // Collected answers
    private bool   $useExistingModel;
    private string $controllerName;
    private string $middlewareName;
    private string $routesFileName;
    private string $viewsPath;
    private bool   $runMigrations;
    private string $routePrefix;

    public function handle(): int
    {
        $this->line('');
        $this->info('🚀 Welcome to Deixtra Laravel Starter Auth Installer');
        $this->line('─────────────────────────────────────────────────────');
        $this->line('');

        $this->askQuestions();

        $this->line('');
        $this->line('⚙️  Installing Deixtra Laravel Starter Auth...');
        $this->line('');

        $this->publishViews();
        $this->publishConfig();
        $this->publishController();
        $this->publishMiddleware();
        $this->publishRoutesFile();

        if (! $this->useExistingModel) {
            $this->createUserModel();
        }

        if ($this->runMigrations) {
            $this->runDatabaseMigrations();
        }

        $this->injectMiddlewareAlias();
        $this->includeRoutesFile();
        $this->saveInstallChoicesToConfig();

        $this->printSuccessSummary();

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Questions
    // ─────────────────────────────────────────────────────────────────────────

    private function askQuestions(): void
    {
        // Q1: User model
        $modelChoice = $this->choice(
            'Q1: Which User model would you like to use?',
            [
                1 => 'Use existing App\\Models\\User (recommended)',
                2 => 'Create a new User model in the package',
            ],
            1
        );
        $this->useExistingModel = str_contains($modelChoice, 'existing');

        // Q2: Controller name
        $controllerName = $this->ask('Q2: What would you like to name the Auth Controller?', 'AuthController');
        $this->controllerName = Str::studly(trim((string) $controllerName) ?: 'AuthController');

        // Q3: Middleware name
        $middlewareName = $this->ask('Q3: What would you like to name the authentication middleware?', 'AuthMiddleware');
        $this->middlewareName = Str::studly(trim((string) $middlewareName) ?: 'AuthMiddleware');

        // Q4: Routes file name
        $routesFileName = $this->ask('Q4: What would you like to name the auth routes file? (without .php)', 'auth');
        $this->routesFileName = Str::slug(trim((string) $routesFileName) ?: 'auth', '-');

        // Q5: Views directory
        $viewChoice = $this->choice(
            'Q5: Where would you like to publish the views?',
            [
                1 => 'resources/views/auth-starter/ (default vendor folder)',
                2 => 'resources/views/ (directly in views root)',
                3 => 'Custom path — I will type it',
            ],
            1
        );

        if (str_contains($viewChoice, 'default vendor')) {
            $this->viewsPath = 'auth-starter';
        } elseif (str_contains($viewChoice, 'directly')) {
            $this->viewsPath = '';
        } else {
            $customPath = $this->ask('Enter custom path relative to resources/views/ (e.g. "vendor/my-auth"):', 'vendor/auth-starter');
            $this->viewsPath = trim((string) $customPath, '/');
        }

        // Q6: Run migrations
        $this->runMigrations = $this->confirm('Q6: Would you like to run migrations now?', true);

        // Q7: Route prefix
        $routePrefix = $this->ask('Q7: What prefix do you want for auth routes? (leave empty for none, e.g. "auth")', '');
        $this->routePrefix = trim((string) $routePrefix, '/');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 1: Publish views
    // ─────────────────────────────────────────────────────────────────────────

    private function publishViews(): void
    {
        $source      = __DIR__ . '/../../resources/views';
        $destination = $this->viewsPath === ''
            ? resource_path('views')
            : resource_path('views/' . $this->viewsPath);

        $this->copyDirectory($source, $destination);
        $this->line('✅ Views published → resources/views/' . ($this->viewsPath ?: '(root)'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 2: Publish config
    // ─────────────────────────────────────────────────────────────────────────

    private function publishConfig(): void
    {
        $source      = __DIR__ . '/../../config/auth-starter.php';
        $destination = config_path('auth-starter.php');

        if (File::exists($destination) && ! $this->option('force')) {
            $this->line('⏭️  Config already exists, skipping (use --force to overwrite)');
            return;
        }

        File::ensureDirectoryExists(config_path());
        File::copy($source, $destination);
        $this->line('✅ Config published → config/auth-starter.php');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 3: Publish controller into app/Http/Controllers/
    // ─────────────────────────────────────────────────────────────────────────

    private function publishController(): void
    {
        $destination = app_path("Http/Controllers/{$this->controllerName}.php");

        if (File::exists($destination) && ! $this->option('force')) {
            $this->line("⏭️  Controller {$this->controllerName}.php already exists, skipping");
            return;
        }

        $stub = $this->buildControllerStub();

        File::ensureDirectoryExists(app_path('Http/Controllers'));
        File::put($destination, $stub);
        $this->line("✅ Controller published → app/Http/Controllers/{$this->controllerName}.php");
    }

    private function buildControllerStub(): string
    {
        $userModelClass = $this->useExistingModel
            ? '\\App\\Models\\User'
            : '\\Deixtra\\LaravelStarterAuth\\Models\\User';

        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class {$this->controllerName} extends Controller
{
    public function showLogin(): View
    {
        return view(config('auth-starter.login_view', 'auth-starter::auth.login'));
    }

    public function login(Request \$request): RedirectResponse
    {
        \$credentials = \$request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(\$credentials, \$request->boolean('remember'))) {
            \$request->session()->regenerate();

            return redirect()->intended(config('auth-starter.home', '/dashboard'));
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view(config('auth-starter.register_view', 'auth-starter::auth.register'));
    }

    public function register(Request \$request): RedirectResponse
    {
        \$validated = \$request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        \$userModel = '{$userModelClass}';

        \$user = \$userModel::create([
            'name'     => \$validated['name'],
            'email'    => \$validated['email'],
            'password' => bcrypt(\$validated['password']),
        ]);

        Auth::login(\$user);

        return redirect(config('auth-starter.home', '/dashboard'));
    }

    public function logout(Request \$request): RedirectResponse
    {
        Auth::logout();

        \$request->session()->invalidate();
        \$request->session()->regenerateToken();

        return redirect()->route('auth-starter.login');
    }
}
PHP;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 4: Publish middleware into app/Http/Middleware/
    // ─────────────────────────────────────────────────────────────────────────

    private function publishMiddleware(): void
    {
        $destination = app_path("Http/Middleware/{$this->middlewareName}.php");

        if (File::exists($destination) && ! $this->option('force')) {
            $this->line("⏭️  Middleware {$this->middlewareName}.php already exists, skipping");
            return;
        }

        $stub = $this->buildMiddlewareStub();

        File::ensureDirectoryExists(app_path('Http/Middleware'));
        File::put($destination, $stub);
        $this->line("✅ Middleware published → app/Http/Middleware/{$this->middlewareName}.php");
    }

    private function buildMiddlewareStub(): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class {$this->middlewareName}
{
    public function handle(Request \$request, Closure \$next, string ...\$guards): Response
    {
        \$guards = empty(\$guards) ? [null] : \$guards;

        foreach (\$guards as \$guard) {
            if (Auth::guard(\$guard)->check()) {
                return redirect(config('auth-starter.home', '/dashboard'));
            }
        }

        return \$next(\$request);
    }
}
PHP;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 5: Publish routes file into routes/
    // ─────────────────────────────────────────────────────────────────────────

    private function publishRoutesFile(): void
    {
        $destination = base_path("routes/{$this->routesFileName}.php");

        if (File::exists($destination) && ! $this->option('force')) {
            $this->line("⏭️  Routes file {$this->routesFileName}.php already exists, skipping");
            return;
        }

        $stub = $this->buildRoutesStub();

        File::ensureDirectoryExists(base_path('routes'));
        File::put($destination, $stub);
        $this->line("✅ Routes published → routes/{$this->routesFileName}.php");
    }

    private function buildRoutesStub(): string
    {
        $middlewareAlias = Str::snake(Str::slug($this->middlewareName, '_'));

        return <<<PHP
<?php

declare(strict_types=1);

use App\Http\Controllers\\{$this->controllerName};
use Deixtra\LaravelStarterAuth\Http\Controllers\PasswordResetController;
use Deixtra\LaravelStarterAuth\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

\$prefix              = config('auth-starter.route_prefix', '');
\$registrationEnabled = config('auth-starter.registration_enabled', true);

Route::prefix(\$prefix)
    ->name('auth-starter.')
    ->group(function () use (\$registrationEnabled): void {

        // Guest-only routes
        Route::middleware('guest')->group(function () use (\$registrationEnabled): void {

            Route::get('/login', [{$this->controllerName}::class, 'showLogin'])
                ->name('login');

            Route::post('/login', [{$this->controllerName}::class, 'login']);

            if (\$registrationEnabled) {
                Route::get('/register', [{$this->controllerName}::class, 'showRegister'])
                    ->name('register');

                Route::post('/register', [{$this->controllerName}::class, 'register']);
            }

            Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])
                ->name('password.request');

            Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
                ->name('password.email');

            Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
                ->name('password.reset');

            Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
                ->name('password.update');
        });

        // Authenticated routes
        Route::middleware('auth')->group(function () use (\$registrationEnabled): void {

            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');

            Route::post('/logout', [{$this->controllerName}::class, 'logout'])
                ->name('logout');
        });
    });
PHP;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 6: Create optional User model
    // ─────────────────────────────────────────────────────────────────────────

    private function createUserModel(): void
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

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AuthUser extends Authenticatable
{
    use CanResetPassword, HasFactory, Notifiable;

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
        $this->line('✅ User model created → app/Models/AuthUser.php');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 7: Run migrations
    // ─────────────────────────────────────────────────────────────────────────

    private function runDatabaseMigrations(): void
    {
        $this->callSilent('vendor:publish', [
            '--tag'   => 'auth-starter-migrations',
            '--force' => $this->option('force'),
        ]);

        $this->call('migrate');
        $this->line('✅ Migrations executed');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 8: Inject middleware alias into bootstrap/app.php
    // ─────────────────────────────────────────────────────────────────────────

    private function injectMiddlewareAlias(): void
    {
        $bootstrapPath = base_path('bootstrap/app.php');

        if (! File::exists($bootstrapPath)) {
            $this->warn('⚠️  bootstrap/app.php not found. Add the middleware alias manually.');
            return;
        }

        $content      = File::get($bootstrapPath);
        $aliasKey     = Str::slug($this->middlewareName, '.');
        $aliasClass   = "App\\Http\\Middleware\\{$this->middlewareName}";

        // Skip if already injected
        if (str_contains($content, $aliasKey) || str_contains($content, $this->middlewareName . '::class')) {
            $this->line("⏭️  Middleware alias already present in bootstrap/app.php");
            return;
        }

        $injection = "\n        \$middleware->alias([\n            '{$aliasKey}' => \\{$aliasClass}::class,\n        ]);\n";

        // Inject inside existing ->withMiddleware() block
        $pattern = '/(->withMiddleware\(\s*function\s*\(\s*(?:Middleware\s+)?\$middleware\s*\)\s*(?::\s*void\s*)?\{)/';

        if (preg_match($pattern, $content)) {
            $updated = preg_replace($pattern, '$1' . $injection, $content, 1);

            if ($updated !== null && $updated !== $content) {
                File::put($bootstrapPath, $updated);
                $this->line("✅ Middleware alias '{$aliasKey}' injected into bootstrap/app.php");
                return;
            }
        }

        $this->warn("⚠️  Could not auto-inject middleware. Add manually to bootstrap/app.php:");
        $this->line("    \$middleware->alias(['{$aliasKey}' => \\{$aliasClass}::class]);");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 9: Include routes file in routes/web.php
    // ─────────────────────────────────────────────────────────────────────────

    private function includeRoutesFile(): void
    {
        $webRoutesPath = base_path('routes/web.php');
        $requireLine   = "require __DIR__.'/{$this->routesFileName}.php';";

        if (! File::exists($webRoutesPath)) {
            $this->warn('⚠️  routes/web.php not found. Include your routes file manually.');
            return;
        }

        $content = File::get($webRoutesPath);

        if (str_contains($content, $this->routesFileName . '.php')) {
            $this->line("⏭️  Routes file already included in routes/web.php");
            return;
        }

        File::append($webRoutesPath, "\n\n" . $requireLine . "\n");
        $this->line("✅ Routes file included in routes/web.php");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 10: Save all choices to published config
    // ─────────────────────────────────────────────────────────────────────────

    private function saveInstallChoicesToConfig(): void
    {
        $configPath = config_path('auth-starter.php');

        if (! File::exists($configPath)) {
            return;
        }

        $content = File::get($configPath);

        $replacements = [
            // route_prefix - replace env() call or existing value
            "/('route_prefix'\s*=>\s*)(?:env\([^)]+\)|'[^']*')/"
                => "'route_prefix' => '" . addslashes($this->routePrefix) . "'",

            // controller_name
            "/('controller_name'\s*=>\s*)'[^']*'/"
                => "'controller_name' => '" . addslashes($this->controllerName) . "'",

            // middleware_name
            "/('middleware_name'\s*=>\s*)'[^']*'/"
                => "'middleware_name' => '" . addslashes(Str::slug($this->middlewareName, '.')) . "'",

            // routes_file
            "/('routes_file'\s*=>\s*)'[^']*'/"
                => "'routes_file' => '" . addslashes($this->routesFileName) . "'",

            // views_path
            "/('views_path'\s*=>\s*)'[^']*'/"
                => "'views_path' => '" . addslashes($this->viewsPath) . "'",

            // use_existing_user_model
            "/('use_existing_user_model'\s*=>\s*)(?:true|false)/"
                => "'use_existing_user_model' => " . ($this->useExistingModel ? 'true' : 'false'),
        ];

        foreach ($replacements as $pattern => $replacement) {
            $updated = preg_replace($pattern, $replacement, $content);
            if ($updated !== null) {
                $content = $updated;
            }
        }

        File::put($configPath, $content);
        $this->line('✅ Install choices saved to config/auth-starter.php');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper: recursively copy directory
    // ─────────────────────────────────────────────────────────────────────────

    private function copyDirectory(string $source, string $destination): void
    {
        File::ensureDirectoryExists($destination);

        /** @var \SplFileInfo[] $files */
        $files = File::allFiles($source);

        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            $target       = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if (File::exists($target) && ! $this->option('force')) {
                continue;
            }

            File::ensureDirectoryExists(dirname($target));
            File::copy($file->getPathname(), $target);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Success summary
    // ─────────────────────────────────────────────────────────────────────────

    private function printSuccessSummary(): void
    {
        $p          = $this->routePrefix ? '/' . $this->routePrefix : '';
        $viewsLabel = $this->viewsPath
            ? "resources/views/{$this->viewsPath}/"
            : 'resources/views/ (root)';

        $this->line('');
        $this->line('─────────────────────────────────────────────────────');
        $this->info('🎉 Deixtra Laravel Starter Auth installed successfully!');
        $this->line('─────────────────────────────────────────────────────');
        $this->line('');
        $this->line('📦 What was installed:');
        $this->line('');
        $this->line("   🖼️  Views        → {$viewsLabel}");
        $this->line("   ⚙️  Config       → config/auth-starter.php");
        $this->line("   🎮 Controller    → app/Http/Controllers/{$this->controllerName}.php");
        $this->line("   🛡️  Middleware   → app/Http/Middleware/{$this->middlewareName}.php");
        $this->line("   🛣️  Routes file  → routes/{$this->routesFileName}.php");

        if (! $this->useExistingModel) {
            $this->line('   👤 User Model   → app/Models/AuthUser.php');
        } else {
            $this->line('   👤 User Model   → Using existing App\\Models\\User');
        }

        if ($this->runMigrations) {
            $this->line('   🗄️  Migrations  → Executed successfully');
        }

        $this->line('');
        $this->line('🌐 Available Routes:');
        $this->line('');
        $this->line("   GET  {$p}/login               → auth-starter.login");
        $this->line("   POST {$p}/login               → (login handler)");
        $this->line("   GET  {$p}/register            → auth-starter.register");
        $this->line("   GET  {$p}/forgot-password     → auth-starter.password.request");
        $this->line("   POST {$p}/forgot-password     → auth-starter.password.email");
        $this->line("   GET  {$p}/reset-password/{token} → auth-starter.password.reset");
        $this->line("   GET  {$p}/dashboard           → auth-starter.dashboard");
        $this->line("   POST {$p}/logout              → auth-starter.logout");
        $this->line('');
        $this->line('📖 Documentation: https://github.com/deixtra/laravel-starter-auth');
        $this->line('');
        $this->info('✨ Powered by Deixtra Private Limited — https://deixtra.com');
        $this->line('');
    }
}
