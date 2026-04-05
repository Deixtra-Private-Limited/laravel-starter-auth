@extends('auth-starter::layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="w-full max-w-5xl">

    {{-- Welcome Header --}}
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-white">
            Welcome, {{ auth()->user()->name }}! 👋
        </h1>
        <p class="text-white/70 mt-2">Here's what's happening with your account.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

        {{-- Card 1 --}}
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#7c24e0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-500 bg-green-50 px-2 py-1 rounded-full">Active</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">Profile</p>
            <p class="text-sm text-gray-500 mt-1">{{ auth()->user()->email }}</p>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#7c24e0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @if(auth()->user()->email_verified_at)
                    <span class="text-xs font-medium text-green-500 bg-green-50 px-2 py-1 rounded-full">Verified</span>
                @else
                    <span class="text-xs font-medium text-yellow-500 bg-yellow-50 px-2 py-1 rounded-full">Unverified</span>
                @endif
            </div>
            <p class="text-2xl font-bold text-gray-800">Email</p>
            <p class="text-sm text-gray-500 mt-1">
                @if(auth()->user()->email_verified_at)
                    Verified on {{ auth()->user()->email_verified_at->format('M d, Y') }}
                @else
                    Not yet verified
                @endif
            </p>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#7c24e0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-blue-500 bg-blue-50 px-2 py-1 rounded-full">Info</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">Member Since</p>
            <p class="text-sm text-gray-500 mt-1">{{ auth()->user()->created_at->format('M d, Y') }}</p>
        </div>

    </div>

    {{-- Account Card --}}
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-[#50016e] to-[#7c24e0] rounded-2xl flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ auth()->user()->name }}</h2>
                    <p class="text-gray-500 text-sm">{{ auth()->user()->email }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">ID: #{{ auth()->user()->id }}</p>
                </div>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('auth-starter.logout') }}">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#50016e] to-[#7c24e0] text-white rounded-xl font-semibold text-sm hover:from-[#3d0057] hover:to-[#6819ca] focus:outline-none focus:ring-2 focus:ring-[#7c24e0] focus:ring-offset-2 transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign out
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
