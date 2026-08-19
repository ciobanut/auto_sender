<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-8">
        {{-- Welcome header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">{{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}</h1>
                <p class="text-sm text-muted-foreground">{{ __('Here\'s what\'s happening with your campaigns today.') }}</p>
            </div>
            <x-ui.button class="hidden sm:flex">
                <x-ui.icon name="tabler.send" class="h-4 w-4" /> {{ __('New Send') }}
            </x-ui.button>
        </div>

        {{-- Stats grid --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.card>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                            <x-ui.icon name="tabler.send" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">{{ __('Total Sends') }}</p>
                            <p class="text-2xl font-bold">12,458</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium text-success">
                        <x-ui.icon name="tabler.trending-up" class="h-3.5 w-3.5" />
                        12.5%
                    </span>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                            <x-ui.icon name="tabler.broadcast" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">{{ __('Active Campaigns') }}</p>
                            <p class="text-2xl font-bold">24</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium text-success">
                        <x-ui.icon name="tabler.trending-up" class="h-3.5 w-3.5" />
                        8.2%
                    </span>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                            <x-ui.icon name="tabler.percentage" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">{{ __('Open Rate') }}</p>
                            <p class="text-2xl font-bold">36.8%</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium text-success">
                        <x-ui.icon name="tabler.trending-up" class="h-3.5 w-3.5" />
                        2.1%
                    </span>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                            <x-ui.icon name="tabler.users" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">{{ __('Contacts') }}</p>
                            <p class="text-2xl font-bold">8,924</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium text-success">
                        <x-ui.icon name="tabler.trending-up" class="h-3.5 w-3.5" />
                        5.7%
                    </span>
                </div>
            </x-ui.card>
        </div>

        {{-- Main content grid --}}
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Recent activity --}}
            <x-ui.card variant="sectioned" class="lg:col-span-2">
                <x-ui.card-header class="flex-row items-center justify-between">
                    <x-ui.card-title>{{ __('Recent Activity') }}</x-ui.card-title>
                    <x-ui.button variant="ghost" size="xs">{{ __('View all') }}</x-ui.button>
                </x-ui.card-header>
                <x-ui.card-content>
                    <div class="divide-y divide-border">
                        @foreach ([
                        ['icon' => 'tabler.send', 'color' => 'text-blue-500', 'title' => 'Campaign "Spring Sale" sent to 2,450 contacts', 'time' => '2 hours ago'],
                        ['icon' => 'tabler.user-plus', 'color' => 'text-green-500', 'title' => '1,230 new contacts imported via CSV', 'time' => '5 hours ago'],
                        ['icon' => 'tabler.template', 'color' => 'text-amber-500', 'title' => 'Email template "Newsletter v3" was updated', 'time' => 'Yesterday'],
                        ['icon' => 'tabler.chart-bar', 'color' => 'text-purple-500', 'title' => 'Weekly analytics report generated', 'time' => 'Yesterday'],
                        ['icon' => 'tabler.settings', 'color' => 'text-muted-foreground', 'title' => 'SMTP configuration for "Main Server" was tested', 'time' => '2 days ago'],
                        ] as $activity)
                        <div class="flex items-start gap-3 px-6 py-4">
                            <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-muted">
                                <x-ui.icon name="{{ $activity['icon'] }}" class="h-4 w-4 {{ $activity['color'] }}" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">{{ $activity['title'] }}</p>
                                <p class="text-xs text-muted-foreground mt-0.5">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-ui.card-content>
            </x-ui.card>

            {{-- Quick actions & upcoming --}}
            <div class="space-y-6">
                {{-- Quick actions --}}
                <x-ui.card variant="sectioned">
                    <x-ui.card-header>
                        <x-ui.card-title>{{ __('Quick Actions') }}</x-ui.card-title>
                    </x-ui.card-header>
                    <x-ui.card-content class="space-y-2">
                        <x-ui.button variant="ghost" class="w-full justify-start gap-3">
                            <x-ui.icon name="tabler.send" class="h-4 w-4 text-blue-500" />
                            {{ __('New Campaign') }}
                        </x-ui.button>
                        <x-ui.button variant="ghost" class="w-full justify-start gap-3">
                            <x-ui.icon name="tabler.user-plus" class="h-4 w-4 text-green-500" />
                            {{ __('Import Contacts') }}
                        </x-ui.button>
                        <x-ui.button variant="ghost" class="w-full justify-start gap-3">
                            <x-ui.icon name="tabler.template" class="h-4 w-4 text-amber-500" />
                            {{ __('Create Template') }}
                        </x-ui.button>
                        <x-ui.button variant="ghost" class="w-full justify-start gap-3">
                            <x-ui.icon name="tabler.settings" class="h-4 w-4 text-muted-foreground" />
                            {{ __('Configure SMTP') }}
                        </x-ui.button>
                    </x-ui.card-content>
                </x-ui.card>

                {{-- Upcoming sends --}}
                <x-ui.card variant="sectioned">
                    <x-ui.card-header>
                        <x-ui.card-title>{{ __('Upcoming Sends') }}</x-ui.card-title>
                    </x-ui.card-header>
                    <x-ui.card-content class="p-0">
                        <div class="divide-y divide-border">
                            @foreach ([
                            ['name' => 'Welcome Series', 'date' => 'Tomorrow 9:00 AM', 'contacts' => 540],
                            ['name' => 'Product Update', 'date' => 'Jun 2, 2:00 PM', 'contacts' => 1,230],
                            ['name' => 'Monthly Newsletter', 'date' => 'Jun 5, 10:00 AM', 'contacts' => 8,450],
                            ] as $send)
                            <div class="px-6 py-3">
                                <p class="text-sm font-medium">{{ $send['name'] }}</p>
                                <div class="flex items-center gap-3 mt-0.5">
                                    <span class="text-xs text-muted-foreground">{{ $send['date'] }}</span>
                                    <span class="text-xs text-muted-foreground">·</span>
                                    <span class="text-xs text-muted-foreground">{{ number_format($send['contacts']) }} contacts</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts::app>
