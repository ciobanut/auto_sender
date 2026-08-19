<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen font-sans antialiased bg-background flex flex-col">
        <header class="w-full px-6 py-4">
            <nav class="flex items-center justify-end gap-4 max-w-7xl mx-auto welcome-nav">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" wire:navigate class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                                {{ __('Register') }}
                            </a>
                        @endif
                        <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                            {{ __('Log in') }}
                        </a>
                    @endauth
                @endif
            </nav>
        </header>

        <main class="flex-1 flex items-center justify-center px-6">
            <div class="max-w-lg text-center space-y-8">
                <div class="flex justify-center">
                    <x-app-logo-icon class="size-16 text-accent welcome-logo" />
                </div>

                <div class="space-y-2">
                    <h1 class="text-4xl font-bold tracking-tight welcome-title">{{ config('app.name', 'Laravel') }}</h1>
                    <p class="text-lg text-muted-foreground welcome-subtitle">
                        {{ __('Send emails at scale. Simple, fast, and reliable.') }}
                    </p>
                </div>

                @auth
                    <div>
                        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90 welcome-cta">
                            {{ __('Go to Dashboard') }}
                        </a>
                    </div>
                @else
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90 welcome-cta">
                            {{ __('Get started') }}
                        </a>
                        <a href="{{ route('register') }}" wire:navigate class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors welcome-cta">
                            {{ __('Create account') }}
                        </a>
                    </div>
                @endauth
            </div>
        </main>

        <footer class="w-full px-6 py-4">
            <p class="text-center text-sm text-muted-foreground">
                &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}
            </p>
        </footer>
    </body>
</html>
