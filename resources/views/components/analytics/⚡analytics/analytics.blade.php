<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Analytics') }}</h1>
        <p class="text-sm text-muted-foreground">{{ __('Track your job application performance and AI effectiveness.') }}</p>
    </div>

    {{-- Stats grid --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                    <x-ui.icon name="tabler.briefcase" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">{{ __('Keywords') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stats['total_keywords'] }}</p>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                    <x-ui.icon name="tabler.send" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">{{ __('Applications') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stats['total_applications'] }}</p>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                    <x-ui.icon name="tabler.message-reply" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">{{ __('Reply Rate') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stats['reply_rate'] }}%</p>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                    <x-ui.icon name="tabler.star" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">{{ __('Interview Rate') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stats['interview_rate'] }}%</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Top keywords --}}
    @if(count($this->topKeywords) > 0)
    <x-ui.card variant="sectioned">
        <x-ui.card-header>
            <x-ui.card-title>{{ __('Top Keywords') }}</x-ui.card-title>
        </x-ui.card-header>
        <x-ui.card-content>
            <div class="space-y-3">
                @foreach($this->topKeywords as $kw)
                <div class="flex items-center justify-between">
                    <span class="font-medium">{{ $kw['keyword'] }}</span>
                    <x-ui.badge variant="soft">{{ $kw['applications_count'] }} {{ __('applications') }}</x-ui.badge>
                </div>
                @endforeach
            </div>
        </x-ui.card-content>
    </x-ui.card>
    @endif
</div>
