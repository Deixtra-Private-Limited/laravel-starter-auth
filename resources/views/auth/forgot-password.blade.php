@extends('auth-starter::layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="w-full max-w-md">

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

        {{-- Card Header --}}
        <div class="bg-gradient-to-r from-[#50016e] to-[#7c24e0] px-8 py-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Forgot password?</h1>
            <p class="text-white/70 mt-1 text-sm">No worries, we'll send you a reset link</p>
        </div>

        {{-- Card Body --}}
        <div class="px-8 py-8">

            {{-- Description --}}
            <p class="text-sm text-gray-500 mb-6 text-center leading-relaxed">
                Enter the email address associated with your account and we'll send you a link to reset your password.
            </p>

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

            <form method="POST" action="{{ route('auth-starter.password.email') }}" class="space-y-5">
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

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-[#50016e] to-[#7c24e0] text-white py-2.5 px-4 rounded-lg font-semibold text-sm hover:from-[#3d0057] hover:to-[#6819ca] focus:outline-none focus:ring-2 focus:ring-[#7c24e0] focus:ring-offset-2 transition-all transform hover:scale-[1.01] active:scale-[0.99]"
                >
                    Send reset link
                </button>
            </form>

            {{-- Back to Login --}}
            <p class="mt-6 text-center text-sm text-gray-500">
                <a href="{{ route('auth-starter.login') }}"
                   class="text-[#7c24e0] hover:text-[#50016e] font-semibold transition-colors inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to login
                </a>
            </p>

        </div>
    </div>

</div>
@endsection
