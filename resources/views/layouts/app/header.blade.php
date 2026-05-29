<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
                    <x-tabler.menu-2 class="cursor-pointer" />
                </label>
            </x-slot:actions>
        </x-nav>

        {{-- Desktop top bar --}}
        <x-nav sticky class="hidden border-b lg:flex bg-base-100">
            <x-slot:brand>
                <label for="main-drawer" class="me-3 lg:hidden">
                    <x-tabler.menu-2 class="cursor-pointer" />
                </label>
                <x-app-brand />
            </x-slot:brand>
            <x-slot:actions>
                <x-button class="btn-ghost btn-sm"><x-tabler.search /></x-button>
                <x-button class="btn-ghost btn-sm" link="https://github.com/laravel/livewire-starter-kit" target="_blank"><x-tabler.folder /></x-button>
                <x-button class="btn-ghost btn-sm" link="https://laravel.com/docs/starter-kits#livewire" target="_blank"><x-tabler.book /></x-button>

                <x-dropdown>
                    <x-slot:trigger>
                        <x-button class="btn-ghost btn-sm"><x-tabler.user /></x-button>
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
                        <x-tabler.settings class="size-4 inline shrink-0" /> {{ __('Settings') }}
                    </x-menu-item>

                    <hr class="my-1" />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <x-menu-item link="{{ route('logout') }}">
                            <x-tabler.logout class="size-4 inline shrink-0" /> {{ __('Log out') }}
                        </x-menu-item>
                    </form>
                </x-dropdown>
            </x-slot:actions>
        </x-nav>

        {{-- Sidebar drawer for mobile --}}
        <x-drawer id="main-drawer" class="bg-base-100">
            <x-app-brand />

            <x-menu activate-by-route>
                <li>
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="my-0.5 py-1.5 px-4 hover:text-inherit whitespace-nowrap flex items-center gap-3 @if(request()->routeIs('dashboard')) mary-active-menu bg-base-300 @endif">
                        <span class="block py-0.5"><x-tabler.home class="size-5 mb-0.5" /></span>
                        <span class="mary-hideable whitespace-nowrap truncate">{{ __('Dashboard') }}</span>
                    </a>
                </li>
            </x-menu>

            <x-menu-separator />

            <x-menu>
                <li>
                    <a href="https://github.com/laravel/livewire-starter-kit" target="_blank"
                        class="my-0.5 py-1.5 px-4 hover:text-inherit whitespace-nowrap flex items-center gap-3">
                        <span class="block py-0.5"><x-tabler.folder class="size-5 mb-0.5" /></span>
                        <span class="mary-hideable whitespace-nowrap truncate">{{ __('Repository') }}</span>
                    </a>
                </li>
                <li>
                    <a href="https://laravel.com/docs/starter-kits#livewire" target="_blank"
                        class="my-0.5 py-1.5 px-4 hover:text-inherit whitespace-nowrap flex items-center gap-3">
                        <span class="block py-0.5"><x-tabler.book class="size-5 mb-0.5" /></span>
                        <span class="mary-hideable whitespace-nowrap truncate">{{ __('Documentation') }}</span>
                    </a>
                </li>
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
