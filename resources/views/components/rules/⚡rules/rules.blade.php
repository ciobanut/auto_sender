<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Sending Rules') }}</h1>
        <p class="text-sm text-muted-foreground">{{ __('Configure cooldowns, limits, and safe mode for automatic applications.') }}</p>
    </div>

    @if($this->rules->isEmpty())
    <x-ui.card>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <x-ui.icon name="tabler.settings" class="text-muted-foreground mb-3 h-12 w-12" />
            <h3 class="text-lg font-medium mb-2">{{ __('No custom rules') }}</h3>
            <p class="text-muted-foreground text-sm">{{ __('Default cooldown of 30 days applies. Add rules to override per keyword or company.') }}</p>
        </div>
    </x-ui.card>
    @else
    <x-ui.card variant="sectioned">
        <x-ui.card-content>
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>{{ __('Type') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Target') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Cooldown') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Max/Period') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Actions') }}</x-ui.table-head>
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @foreach($this->rules as $rule)
                    <x-ui.table-row>
                        <x-ui.table-cell>{{ $rule->keyword_id ? __('Keyword') : __('Company') }}</x-ui.table-cell>
                        <x-ui.table-cell class="font-medium">{{ $rule->keyword?->keyword ?? $rule->company_domain }}</x-ui.table-cell>
                        <x-ui.table-cell>{{ $rule->cooldown_hours }}h</x-ui.table-cell>
                        <x-ui.table-cell>{{ $rule->max_applications }}/{{ $rule->period_hours }}h</x-ui.table-cell>
                        <x-ui.table-cell>
                            <div class="flex items-center gap-1">
                                <x-ui.button variant="ghost" size="icon-xs">
                                    <x-ui.icon name="tabler.pencil" class="h-3.5 w-3.5" />
                                </x-ui.button>
                                <x-ui.button variant="ghost" size="icon-xs" class="text-destructive">
                                    <x-ui.icon name="tabler.trash" class="h-3.5 w-3.5" />
                                </x-ui.button>
                            </div>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                    @endforeach
                </x-ui.table-body>
            </x-ui.table>
        </x-ui.card-content>
    </x-ui.card>
    @endif
</div>
