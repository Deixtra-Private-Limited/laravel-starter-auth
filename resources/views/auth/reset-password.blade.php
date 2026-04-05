@extends('auth-starter::layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="w-full max-w-md">

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

        {{-- Card Header --}}
        <div class="bg-gradient-to-r from-[#50016e] to-[#7c24e0] px-8 py-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Reset password</h1>
            <p class="text-white/70 mt-1 text-sm">Choose a strong new password</p>
        </div>

        {{-- Card Body --}}
        <div class="px-8 py-8">

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

            <form method="POST" action="{{ route('auth-starter.password.update') }}" class="space-y-5">
                @csrf

                {{-- Hidden Token --}}
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $email) }}"
                        required
                        autocomplete="email"
                        autofocus
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#7c24e0] focus:border-transparent transition-all @error('email') border-red-400 bg-red-50 @enderror"
                        placeholder="you@example.com"
                    >
                </div>

                {{-- New Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        New password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#7c24e0] focus:border-transparent transition-all @error('password') border-red-400 bg-red-50 @enderror"
                        placeholder="Min. 8 characters"
                    >
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Confirm new password
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#7c24e0] focus:border-transparent transition-all @error('password_confirmation') border-red-400 bg-red-50 @enderror"
                        placeholder="Repeat new password"
                    >
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-[#50016e] to-[#7c24e0] text-white py-2.5 px-4 rounded-lg font-semibold text-sm hover:from-[#3d0057] hover:to-[#6819ca] focus:outline-none focus:ring-2 focus:ring-[#7c24e0] focus:ring-offset-2 transition-all transform hover:scale-[1.01] active:scale-[0.99]"
                >
                    Reset password
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
