<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('Application Log') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('Track all your sent applications and responses.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.select wire:model.live="filter" native size="sm" class="w-40">
                <option value="">{{ __('All') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="delivered">{{ __('Delivered') }}</option>
                <option value="failed">{{ __('Failed') }}</option>
                <option value="bounced">{{ __('Bounced') }}</option>
            </x-ui.select>
        </div>
    </div>

    @if($this->applications->isEmpty())
    <x-ui.card>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <x-ui.icon name="tabler.history" class="text-muted-foreground mb-3 h-12 w-12" />
            <h3 class="text-lg font-medium mb-2">{{ __('No applications yet') }}</h3>
            <p class="text-muted-foreground text-sm">{{ __('Applications will appear here once you start sending them.') }}</p>
        </div>
    </x-ui.card>
    @else
    <x-ui.card variant="sectioned">
        <x-ui.card-content>
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>{{ __('Job') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Company') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                        <x-ui.table-head class="hidden sm:table-cell">{{ __('Sent') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Response') }}</x-ui.table-head>
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @foreach($this->applications as $app)
                    <x-ui.table-row>
                        <x-ui.table-cell class="font-medium max-w-xs truncate">{{ $app->jobLink?->title ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell>{{ $app->jobLink?->company_name ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            <x-ui.badge
                                :tone="$app->delivery_status === 'delivered' ? 'success' : ($app->delivery_status === 'failed' ? 'danger' : ($app->delivery_status === 'pending' ? 'neutral' : 'warning'))"
                                variant="soft" class="text-xs"
                            >{{ $app->delivery_status }}</x-ui.badge>
                        </x-ui.table-cell>
                        <x-ui.table-cell class="text-sm text-muted-foreground hidden sm:table-cell">{{ $app->sent_at?->diffForHumans() ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            @if($app->response_received)
                                <x-ui.badge
                                    :tone="$app->response_type === 'interview' ? 'success' : 'danger'"
                                    variant="soft" class="text-xs"
                                >{{ $app->response_type }}</x-ui.badge>
                            @else
                                <span class="text-sm text-muted-foreground">{{ __('Awaiting') }}</span>
                            @endif
                        </x-ui.table-cell>
                    </x-ui.table-row>
                    @endforeach
                </x-ui.table-body>
            </x-ui.table>
        </x-ui.card-content>
    </x-ui.card>
    @endif
</div>
