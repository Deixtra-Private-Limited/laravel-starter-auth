<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Auth') — {{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:  '#f5f0ff',
                            100: '#ede0ff',
                            200: '#d9c0ff',
                            300: '#c49cff',
                            400: '#ac72ff',
                            500: '#9448f7',
                            600: '#7c24e0',
                            700: '#6a19c0',
                            800: '#57149d',
                            900: '#50016e',
                            950: '#2d0040',
                        },
                    },
                },
            },
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #50016e 0%, #7c24e0 50%, #ac72ff 100%);
            min-height: 100vh;
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased">

    <div class="min-h-screen flex flex-col">

        {{-- Navigation --}}
        <nav class="bg-white/10 backdrop-blur-sm border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 text-white font-bold text-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>
            </div>
        </nav>

        {{-- Main Content --}}
        <main class="flex-1 flex items-center justify-center p-4 py-12">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="text-center py-6 text-white/50 text-sm">
            <p>Powered by <a href="https://deixtra.com" target="_blank" class="text-white/70 hover:text-white transition-colors font-medium">Deixtra</a> &mdash; Laravel Starter Auth</p>
        </footer>

    </div>

    @stack('scripts')
</body>
</html>
