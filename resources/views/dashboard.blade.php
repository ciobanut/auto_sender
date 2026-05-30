<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-8">
        {{-- Welcome header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">{{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Here\'s what\'s happening with your campaigns today.') }}</p>
            </div>
            <x-button variant="primary" class="hidden sm:flex">
                <x-icon name="tabler.send" class="w-4 h-4" /> {{ __('New Send') }}
            </x-button>
        </div>

        {{-- Stats grid --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-base-100 rounded-xl border border-base-content/5 p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                            <x-icon name="tabler.send" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total Sends') }}</p>
                            <p class="text-2xl font-bold">12,458</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium text-green-600 dark:text-green-400">
                        <x-icon name="tabler.trending-up" class="w-3.5 h-3.5" />
                        12.5%
                    </span>
                </div>
            </div>

            <div class="bg-base-100 rounded-xl border border-base-content/5 p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                            <x-icon name="tabler.broadcast" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Active Campaigns') }}</p>
                            <p class="text-2xl font-bold">24</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium text-green-600 dark:text-green-400">
                        <x-icon name="tabler.trending-up" class="w-3.5 h-3.5" />
                        8.2%
                    </span>
                </div>
            </div>

            <div class="bg-base-100 rounded-xl border border-base-content/5 p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                            <x-icon name="tabler.percentage" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Open Rate') }}</p>
                            <p class="text-2xl font-bold">36.8%</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium text-green-600 dark:text-green-400">
                        <x-icon name="tabler.trending-up" class="w-3.5 h-3.5" />
                        2.1%
                    </span>
                </div>
            </div>

            <div class="bg-base-100 rounded-xl border border-base-content/5 p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                            <x-icon name="tabler.users" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Contacts') }}</p>
                            <p class="text-2xl font-bold">8,924</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium text-green-600 dark:text-green-400">
                        <x-icon name="tabler.trending-up" class="w-3.5 h-3.5" />
                        5.7%
                    </span>
                </div>
            </div>
        </div>

        {{-- Main content grid --}}
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Recent activity --}}
            <div class="lg:col-span-2 bg-base-100 rounded-xl border border-base-content/5">
                <div class="flex items-center justify-between border-b border-base-content/5 px-6 py-4">
                    <h2 class="font-semibold">{{ __('Recent Activity') }}</h2>
                    <x-button variant="ghost" class="btn-xs">{{ __('View all') }}</x-button>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ([
                    ['icon' => 'tabler.send', 'color' => 'text-blue-500', 'title' => 'Campaign "Spring Sale" sent to 2,450 contacts', 'time' => '2 hours ago'],
                    ['icon' => 'tabler.user-plus', 'color' => 'text-green-500', 'title' => '1,230 new contacts imported via CSV', 'time' => '5 hours ago'],
                    ['icon' => 'tabler.template', 'color' => 'text-amber-500', 'title' => 'Email template "Newsletter v3" was updated', 'time' => 'Yesterday'],
                    ['icon' => 'tabler.chart-bar', 'color' => 'text-purple-500', 'title' => 'Weekly analytics report generated', 'time' => 'Yesterday'],
                    ['icon' => 'tabler.settings', 'color' => 'text-zinc-500', 'title' => 'SMTP configuration for "Main Server" was tested', 'time' => '2 days ago'],
                    ] as $activity)
                    <div class="flex items-start gap-3 px-6 py-4">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                            <x-icon name="{{ $activity['icon'] }}" class="w-4 h-4 {{ $activity['color'] }}" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ $activity['title'] }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick actions & upcoming --}}
            <div class="space-y-6">
                {{-- Quick actions --}}
                <div class="bg-base-100 rounded-xl border border-base-content/5">
                    <div class="border-b border-base-content/5 px-6 py-4">
                        <h2 class="font-semibold">{{ __('Quick Actions') }}</h2>
                    </div>
                    <div class="p-4 space-y-2">
                        <x-button class="btn-ghost btn-sm w-full justify-start gap-3">
                            <x-icon name="tabler.send" class="w-4 h-4 text-blue-500" />
                            {{ __('New Campaign') }}
                        </x-button>
                        <x-button class="btn-ghost btn-sm w-full justify-start gap-3">
                            <x-icon name="tabler.user-plus" class="w-4 h-4 text-green-500" />
                            {{ __('Import Contacts') }}
                        </x-button>
                        <x-button class="btn-ghost btn-sm w-full justify-start gap-3">
                            <x-icon name="tabler.template" class="w-4 h-4 text-amber-500" />
                            {{ __('Create Template') }}
                        </x-button>
                        <x-button class="btn-ghost btn-sm w-full justify-start gap-3">
                            <x-icon name="tabler.settings" class="w-4 h-4 text-zinc-500" />
                            {{ __('Configure SMTP') }}
                        </x-button>
                    </div>
                </div>

                {{-- Upcoming sends --}}
                <div class="bg-base-100 rounded-xl border border-base-content/5">
                    <div class="border-b border-base-content/5 px-6 py-4">
                        <h2 class="font-semibold">{{ __('Upcoming Sends') }}</h2>
                    </div>
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ([
                        ['name' => 'Welcome Series', 'date' => 'Tomorrow 9:00 AM', 'contacts' => 540],
                        ['name' => 'Product Update', 'date' => 'Jun 2, 2:00 PM', 'contacts' => 1,230],
                        ['name' => 'Monthly Newsletter', 'date' => 'Jun 5, 10:00 AM', 'contacts' => 8,450],
                        ] as $send)
                        <div class="px-6 py-3">
                            <p class="text-sm font-medium">{{ $send['name'] }}</p>
                            <div class="flex items-center gap-3 mt-0.5">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $send['date'] }}</span>
                                <span class="text-xs text-zinc-400">·</span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ number_format($send['contacts']) }} contacts</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
