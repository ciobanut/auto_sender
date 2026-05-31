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
        <x-slot:sidebar drawer="main-drawer" class="bg-base-100 lg:bg-inherit h-dvh">

            {{-- BRAND --}}
            <x-app-brand class="px-5 pt-4 pb-8" />

            {{-- MAIN NAVIGATION --}}
            <x-menu activate-by-route>

                <div class="px-4 mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-base-content/60">{{ __('Recruitment') }}</span>
                </div>

                <x-menu-item exact title="{{ __('Dashboard') }}" icon="tabler.home" link="{{ route('dashboard') }}" />
                    <x-menu-item exact title=" {{ __('Job Categories') }}" icon="tabler.category" link="{{ route('keywords') }}" />
                    <x-menu-item exact title=" {{ __('CV Manager') }}" icon="tabler.file-text" link="{{ route('cvs') }}" />
                    <x-menu-item exact title=" {{ __('Extra Skills') }}" icon="tabler.school" link="{{ route('skills') }}" />

                    <div class=" px-4 mt-4 mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-base-content/60">{{ __('AI & Automation') }}</span>
                    </div>

                    <x-menu-item exact title="{{ __('AI Settings') }}" icon="tabler.brain" link="{{ route('ai-settings') }}" />
                    <x-menu-item exact title=" {{ __('Sending Rules') }}" icon="tabler.settings" link="{{ route('rules') }}" />

                    <div class=" px-4 mt-4 mb-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-base-content/60">{{ __('Insights') }}</span>
                        </div>

                        <x-menu-item exact title="{{ __('Analytics') }}" icon="tabler.chart-bar" link="{{ route('analytics') }}" />
                    <x-menu-item exact title=" {{ __('Application Log') }}" icon="tabler.history" link="{{ route('applications.log') }}" />
                </x-menu>
                <x-theme-toggle class=" btn-ghost btn-sm px-5 pt-4" />



                        <x-dropdown>
                            <x-slot:trigger>
                                <x-button class="btn-ghost btn-sm">
                                    <x-icon name="tabler.user" />
                                </x-button>
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

                            <x-menu-item exact link="{{ route('profile.edit') }}">
                                <x-icon name="tabler.settings" class="w-4 h-4" /> {{ __('Settings') }}
                            </x-menu-item>

                            <hr class="my-1" />

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm cursor-pointer hover:bg-base-200 rounded-lg transition-colors">
                                    <x-icon name="tabler.logout" class="w-4 h-4" /> {{ __('Log out') }}
                                </button>
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
