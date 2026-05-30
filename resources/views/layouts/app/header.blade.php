<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="aether">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen font-sans antialiased bg-base-200">

        {{-- Top navbar --}}
        <x-nav sticky class="lg:hidden">
            <x-slot:brand>
                <x-app-brand />
            </x-slot:brand>
            <x-slot:actions>
                <label for="main-drawer" class="lg:hidden me-3">
                    <x-icon name="tabler.menu-2" class="cursor-pointer" />
                </label>
            </x-slot:actions>
        </x-nav>

        {{-- Desktop top bar --}}
        <x-nav sticky class="hidden border-b lg:flex bg-base-100 mb-4">
            <x-slot:brand>
                <label for="main-drawer" class="me-3 lg:hidden">
                    <x-icon name="tabler.menu-2" class="cursor-pointer" />
                </label>
                <x-app-brand />
            </x-slot:brand>
        </x-nav>

        {{-- Sidebar drawer for mobile --}}
        <x-drawer id="main-drawer" class="bg-base-100">
            <x-app-brand />

            <x-menu activate-by-route>
                <x-menu-separator />

                <li class="px-4 mb-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Main</span>
                </li>

                <x-menu-item title="{{ __('Dashboard') }}" icon="tabler.home" :href="route('dashboard')" wire:navigate />

                <li class="px-4 mt-4 mb-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Marketing</span>
                </li>

                <x-menu-item title="{{ __('Broadcasts') }}" icon="tabler.broadcast" :href="route('dashboard')" wire:navigate />
                <x-menu-item title="{{ __('Contacts') }}" icon="tabler.users" :href="route('dashboard')" wire:navigate />
                <x-menu-item title="{{ __('Templates') }}" icon="tabler.template" :href="route('dashboard')" wire:navigate />

                <li class="px-4 mt-4 mb-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Insights</span>
                </li>

                <x-menu-item title="{{ __('Analytics') }}" icon="tabler.chart-bar" :href="route('dashboard')" wire:navigate />
                <x-menu-item title="{{ __('Reports') }}" icon="tabler.file-report" :href="route('dashboard')" wire:navigate />
            </x-menu>
        </x-drawer>

        {{-- Main content --}}
        <main class="p-4 lg:p-8">
            {{ $slot }}
        </main>

        {{-- TOAST --}}
        <x-toast />
    </body>
</html>
