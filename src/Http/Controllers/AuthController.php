<?php

declare(strict_types=1);

namespace Deixtra\LaravelStarterAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view(config('auth-starter.login_view', 'auth-starter::auth.login'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

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

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        $userModel = $this->resolveUserModel();

        $user = $userModel::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        Auth::login($user);

        return redirect(config('auth-starter.home', '/dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth-starter.login');
    }

    protected function resolveUserModel(): string
    {
        if (config('auth-starter.use_existing_user_model', true)) {
            return \App\Models\User::class;
        }

        return \Deixtra\LaravelStarterAuth\Models\User::class;
    }
}
