<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="aether">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen font-sans antialiased bg-base-200">

        {{-- Mobile navbar --}}
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

        {{-- MAIN --}}
        <x-main>
            <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

                {{-- BRAND --}}
                <x-app-brand class="px-5 pt-4" />

                {{-- MAIN NAVIGATION --}}
                <x-menu activate-by-route>
                    <x-menu-separator />

                    <div class="px-4 mb-1">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Recruitment') }}</span>
                    </div>

                    <x-menu-item title="{{ __('Dashboard') }}" icon="tabler.home" :href="route('dashboard')" wire:navigate />
                    <x-menu-item title="{{ __('Job Categories') }}" icon="tabler.category" :href="route('keywords')" wire:navigate />
                    <x-menu-item title="{{ __('CV Manager') }}" icon="tabler.file-text" :href="route('cvs')" wire:navigate />
                    <x-menu-item title="{{ __('Extra Skills') }}" icon="tabler.tools" :href="route('skills')" wire:navigate />

                    <div class="px-4 mt-4 mb-1">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('AI & Automation') }}</span>
                    </div>

                    <x-menu-item title="{{ __('AI Settings') }}" icon="tabler.bot" :href="route('ai-settings')" wire:navigate />
                    <x-menu-item title="{{ __('Sending Rules') }}" icon="tabler.settings" :href="route('rules')" wire:navigate />

                    <div class="px-4 mt-4 mb-1">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Insights') }}</span>
                    </div>

                    <x-menu-item title="{{ __('Analytics') }}" icon="tabler.chart-bar" :href="route('analytics')" wire:navigate />
                    <x-menu-item title="{{ __('Application Log') }}" icon="tabler.history" :href="route('applications.log')" wire:navigate />
                </x-menu>
                <x-theme-toggle class="btn-ghost btn-sm px-5 pt-4" />



                <x-dropdown>
                    <x-slot:trigger>
                        <x-button class="btn-ghost btn-sm"><x-icon name="tabler.user" /></x-button>
                    </x-slot:trigger>

                    <div class="p-2 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <x-avatar :image="null" class="!w-8 !h-8" />
                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-medium">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs opacity-50">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-1" />

                    <x-menu-item :href="route('profile.edit')" wire:navigate>
                        <x-icon name="tabler.settings" class="w-4 h-4" /> {{ __('Settings') }}
                    </x-menu-item>

                    <hr class="my-1" />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <x-menu-item link="{{ route('logout') }}">
                            <x-icon name="tabler.logout" class="w-4 h-4" /> {{ __('Log out') }}
                        </x-menu-item>
                    </form>
                </x-dropdown>

            </x-slot:sidebar>

            {{-- CONTENT --}}
            <x-slot:content>
                {{ $slot }}
            </x-slot:content>
        </x-main>

        {{-- TOAST --}}
        <x-toast />
    </body>
</html>
