@extends('auth-starter::layouts.app')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md">

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

        {{-- Card Header --}}
        <div class="bg-gradient-to-r from-[#50016e] to-[#7c24e0] px-8 py-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Welcome back</h1>
            <p class="text-white/70 mt-1 text-sm">Sign in to your account</p>
        </div>

        {{-- Card Body --}}
        <div class="px-8 py-8">

            {{-- Session Status --}}
            @if (session('status'))
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('auth-starter.login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#7c24e0] focus:border-transparent transition-all @error('email') border-red-400 bg-red-50 @enderror"
                        placeholder="you@example.com"
                    >
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <a href="{{ route('auth-starter.password.request') }}"
                           class="text-xs text-[#7c24e0] hover:text-[#50016e] transition-colors font-medium">
                            Forgot password?
                        </a>
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#7c24e0] focus:border-transparent transition-all @error('password') border-red-400 bg-red-50 @enderror"
                        placeholder="••••••••"
                    >
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                        class="w-4 h-4 text-[#7c24e0] border-gray-300 rounded focus:ring-[#7c24e0]"
                    >
                    <label for="remember" class="ml-2 text-sm text-gray-600">
                        Remember me
                    </label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-[#50016e] to-[#7c24e0] text-white py-2.5 px-4 rounded-lg font-semibold text-sm hover:from-[#3d0057] hover:to-[#6819ca] focus:outline-none focus:ring-2 focus:ring-[#7c24e0] focus:ring-offset-2 transition-all transform hover:scale-[1.01] active:scale-[0.99]"
                >
                    Sign in
                </button>
            </form>

            {{-- Register Link --}}
            @if (config('auth-starter.registration_enabled', true))
                <p class="mt-6 text-center text-sm text-gray-500">
                    Don't have an account?
                    <a href="{{ route('auth-starter.register') }}"
                       class="text-[#7c24e0] hover:text-[#50016e] font-semibold transition-colors">
                        Create one
                    </a>
                </p>
            @endif

        </div>
    </div>

</div>
@endsection
