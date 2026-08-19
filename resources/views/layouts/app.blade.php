<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-background font-sans antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar (desktop) --}}
        <aside class="bg-card hidden w-64 shrink-0 flex-col border-r lg:flex">
            @include('templates.partials.dashboard-sidebar')
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Topbar --}}
            <header class="bg-background/80 supports-[backdrop-filter]:bg-background/60 sticky top-0 z-30 flex h-16 items-center gap-3 border-b px-4 backdrop-blur-xl lg:px-6">
                {{-- Mobile menu --}}
                <x-ui.sheet>
                    <x-ui.sheet-trigger class="lg:hidden">
                        <x-ui.button variant="outline" size="icon" aria-label="Menu">
                            <x-ui.icon name="tabler.menu" class="h-4 w-4" />
                        </x-ui.button>
                    </x-ui.sheet-trigger>
                    <x-ui.sheet-content side="left" class="w-64 p-0">
                        <div class="flex h-full flex-col">
                            @include('templates.partials.dashboard-sidebar')
                        </div>
                    </x-ui.sheet-content>
                </x-ui.sheet>

                {{-- Search --}}
                <div class="relative hidden sm:block">
                    <x-ui.input type="search" placeholder="Search…" class="h-9 w-56 pe-12" />
                </div>

                {{-- Right actions --}}
                <div class="ml-auto flex items-center gap-1.5">
                    {{-- Theme toggle --}}
                    <x-theme-toggle />

                    {{-- Notifications --}}
                    <x-ui.dropdown-menu>
                        <x-ui.dropdown-menu-trigger>
                            <button class="hover:bg-accent relative inline-flex size-9 items-center justify-center rounded-md transition-colors" aria-label="Notifications">
                                <x-ui.icon name="tabler.bell" class="h-4 w-4" />
                                <span class="bg-destructive absolute right-2 top-2 size-2 rounded-full"></span>
                            </button>
                        </x-ui.dropdown-menu-trigger>
                        <x-ui.dropdown-menu-content align="end" class="w-72">
                            <x-ui.dropdown-menu-label>Notifications</x-ui.dropdown-menu-label>
                            <x-ui.dropdown-menu-separator />
                            <x-ui.dropdown-menu-item class="whitespace-normal">New order received</x-ui.dropdown-menu-item>
                            <x-ui.dropdown-menu-item class="whitespace-normal">Payment processed</x-ui.dropdown-menu-item>
                            <x-ui.dropdown-menu-item class="whitespace-normal">Server status updated</x-ui.dropdown-menu-item>
                        </x-ui.dropdown-menu-content>
                    </x-ui.dropdown-menu>
                </div>
            </header>

            {{-- Main content --}}
            <main class="flex-1 space-y-6 p-4 lg:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Toast notifications --}}
    <x-ui.sonner />
</body>
</html>
