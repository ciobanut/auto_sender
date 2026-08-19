<div class="flex h-full flex-col">
    <!-- Logo -->
    <div class="flex h-14 items-center border-b px-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold">
            @include('components.app-logo')
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 p-2">
        {{-- Recruitment --}}
        <div class="px-3 py-2">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ __('Recruitment') }}</p>
        </div>

        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-colors"
           wire:navigate>
            <x-ui.icon name="tabler.layout-dashboard" class="h-4 w-4" />
            {{ __('Dashboard') }}
        </a>

        <a href="{{ route('keywords') }}" 
           class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('keywords') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-colors"
           wire:navigate>
            <x-ui.icon name="tabler.category" class="h-4 w-4" />
            {{ __('Job Categories') }}
        </a>

        <a href="{{ route('cvs') }}" 
           class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('cvs') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-colors"
           wire:navigate>
            <x-ui.icon name="tabler.file-text" class="h-4 w-4" />
            {{ __('CV Manager') }}
        </a>

        <a href="{{ route('skills') }}" 
           class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('skills') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-colors"
           wire:navigate>
            <x-ui.icon name="tabler.school" class="h-4 w-4" />
            {{ __('Extra Skills') }}
        </a>

        {{-- AI & Automation --}}
        <div class="px-3 py-2 mt-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ __('AI & Automation') }}</p>
        </div>

        <a href="{{ route('ai-settings') }}" 
           class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('ai-settings') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-colors"
           wire:navigate>
            <x-ui.icon name="tabler.brain" class="h-4 w-4" />
            {{ __('AI Settings') }}
        </a>

        <a href="{{ route('rules') }}" 
           class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('rules') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-colors"
           wire:navigate>
            <x-ui.icon name="tabler.settings" class="h-4 w-4" />
            {{ __('Sending Rules') }}
        </a>

        {{-- Insights --}}
        <div class="px-3 py-2 mt-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ __('Insights') }}</p>
        </div>

        <a href="{{ route('analytics') }}" 
           class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('analytics') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-colors"
           wire:navigate>
            <x-ui.icon name="tabler.chart-bar" class="h-4 w-4" />
            {{ __('Analytics') }}
        </a>

        <a href="{{ route('applications.log') }}" 
           class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('applications.log') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-colors"
           wire:navigate>
            <x-ui.icon name="tabler.history" class="h-4 w-4" />
            {{ __('Application Log') }}
        </a>
    </nav>

    <!-- User Menu -->
    <div class="border-t p-2">
        <x-ui.dropdown-menu>
            <x-ui.dropdown-menu-trigger class="w-full">
                <div class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors cursor-pointer">
                    <x-ui.avatar class="h-8 w-8">
                        <x-ui.avatar-fallback>{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</x-ui.avatar-fallback>
                    </x-ui.avatar>
                    <div class="flex flex-1 flex-col items-start text-left">
                        <span class="truncate font-medium">{{ auth()->user()->name }}</span>
                        <span class="truncate text-xs text-muted-foreground">{{ auth()->user()->email }}</span>
                    </div>
                    <x-ui.icon name="tabler.chevron-up" class="h-4 w-4" />
                </div>
            </x-ui.dropdown-menu-trigger>
            <x-ui.dropdown-menu-content align="start" class="w-56">
                <x-ui.dropdown-menu-label>{{ __('My Account') }}</x-ui.dropdown-menu-label>
                <x-ui.dropdown-menu-separator />
                <x-ui.dropdown-menu-item href="{{ route('profile.edit') }}">
                    <x-ui.icon name="tabler.user" class="mr-2 h-4 w-4" />
                    {{ __('Profile') }}
                </x-ui.dropdown-menu-item>
                <x-ui.dropdown-menu-item href="{{ route('security.edit') }}">
                    <x-ui.icon name="tabler.lock" class="mr-2 h-4 w-4" />
                    {{ __('Security') }}
                </x-ui.dropdown-menu-item>
                <x-ui.dropdown-menu-item href="{{ route('appearance.edit') }}">
                    <x-ui.icon name="tabler.palette" class="mr-2 h-4 w-4" />
                    {{ __('Appearance') }}
                </x-ui.dropdown-menu-item>
                <x-ui.dropdown-menu-separator />
                <x-ui.dropdown-menu-item>
                    <x-theme-toggle class="w-full justify-start" />
                </x-ui.dropdown-menu-item>
                <x-ui.dropdown-menu-separator />
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <x-ui.dropdown-menu-item>
                        <button type="submit" class="flex w-full items-center">
                            <x-ui.icon name="tabler.logout" class="mr-2 h-4 w-4" />
                            {{ __('Log out') }}
                        </button>
                    </x-ui.dropdown-menu-item>
                </form>
            </x-ui.dropdown-menu-content>
        </x-ui.dropdown-menu>
    </div>
</div>
