<div class="space-y-6">
    {{-- Welcome header --}}
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Recruitment Dashboard') }}</h1>
        <p class="text-sm text-muted-foreground">{{ __('Manage your automated job applications pipeline.') }}</p>
    </div>

    {{-- Pipeline progress bar --}}
    <x-ui.card>
        <div class="flex items-center justify-between gap-2 overflow-x-auto px-2 pb-2">
            @php
                $stages = [
                    ['key' => 'fetch', 'label' => 'Fetch Jobs', 'icon' => 'tabler.download'],
                    ['key' => 'analyze', 'label' => 'Analyze', 'icon' => 'tabler.search'],
                    ['key' => 'generate', 'label' => 'Generate', 'icon' => 'tabler.messages'],
                    ['key' => 'review', 'label' => 'Review', 'icon' => 'tabler.eye'],
                    ['key' => 'send', 'label' => 'Send', 'icon' => 'tabler.send'],
                ];
            @endphp

            @foreach ($stages as $stage)
                @php $count = $this->stageCounts[$stage['key']] ?? 0; @endphp
                <a href="{{ route('pipeline', ['stage' => $stage['key']]) }}"
                   class="flex flex-col items-center gap-2 rounded-lg px-4 py-3 text-muted-foreground hover:text-foreground transition-colors">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full border border-border bg-background">
                        <x-ui.icon name="{{ $stage['icon'] }}" class="h-5 w-5" />
                    </div>
                    <span class="text-xs font-medium">{{ $stage['label'] }}</span>
                    @if($count > 0)
                        <x-ui.badge size="sm">{{ $count }}</x-ui.badge>
                    @endif
                </a>
                @if(!$loop->last)
                    <div class="mt-[-1rem] h-px flex-1 bg-border"></div>
                @endif
            @endforeach
        </div>
    </x-ui.card>

    {{-- Stats grid --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                    <x-ui.icon name="tabler.briefcase" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">{{ __('Total Jobs') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stageCounts['fetch'] }}</p>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                    <x-ui.icon name="tabler.messages" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">{{ __('AI Drafts') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stageCounts['generate'] }}</p>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                    <x-ui.icon name="tabler.eye" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">{{ __('Needs Review') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stageCounts['review'] }}</p>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                    <x-ui.icon name="tabler.send" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">{{ __('Pending Send') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stageCounts['send'] }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
