<div class="space-y-4" @if($isSending) wire:poll.5s="pollSend" @endif>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-semibold">{{ __('Send Applications') }}</h3>
            <p class="text-xs text-muted-foreground">{{ __('Send approved applications with CV and cover letter.') }}</p>
        </div>
        <x-ui.button wire:click="send" wire:loading.attr="disabled" :disabled="$this->approvedLetters->isEmpty()">
            <x-ui.icon name="tabler.send" class="h-4 w-4" wire:loading.remove />
            <span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
            {{ __('Send Applications') }}
        </x-ui.button>
    </div>

    {{-- Ready to send --}}
    @if($this->approvedLetters->isNotEmpty())
    <x-ui.card class="border-success/50 bg-success/5">
        <div class="flex items-center gap-2 mb-3">
            <x-ui.icon name="tabler.send" class="h-4 w-4 text-success" />
            <h4 class="text-sm font-medium">{{ __('Ready to Send') }} ({{ $this->approvedLetters->count() }})</h4>
        </div>
        <div class="space-y-2">
            @foreach($this->approvedLetters as $letter)
            <div class="flex items-center justify-between text-sm py-1">
                <div class="flex items-center gap-2">
                    <span class="font-medium">{{ $letter->jobLink?->title }}</span>
                    <span class="text-muted-foreground text-xs">{{ $letter->jobLink?->company_name }}</span>
                </div>
                <x-ui.badge tone="success" variant="soft" class="text-xs">{{ __('Approved') }}</x-ui.badge>
            </div>
            @endforeach
        </div>
    </x-ui.card>
    @endif

    {{-- Sent applications --}}
    @if($this->sentApplications->isNotEmpty())
    <x-ui.card variant="sectioned">
        <x-ui.card-header>
            <x-ui.card-title>{{ __('Sent Applications') }} ({{ $this->sentApplications->count() }})</x-ui.card-title>
        </x-ui.card-header>
        <x-ui.card-content>
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>{{ __('Job') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Company') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Sent') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Response') }}</x-ui.table-head>
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @foreach($this->sentApplications as $app)
                    <x-ui.table-row>
                        <x-ui.table-cell class="font-medium text-sm max-w-xs truncate">{{ $app->jobLink?->title ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-sm">{{ $app->jobLink?->company_name ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-sm text-muted-foreground">{{ $app->sent_at?->diffForHumans() ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            <x-ui.badge
                                :tone="$app->delivery_status === 'delivered' ? 'success' : ($app->delivery_status === 'pending' ? 'neutral' : ($app->delivery_status === 'failed' ? 'danger' : 'warning'))"
                                variant="soft" class="text-xs"
                            >{{ $app->delivery_status }}</x-ui.badge>
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            @if($app->response_received)
                                <x-ui.badge
                                    :tone="$app->response_type === 'interview' ? 'success' : ($app->response_type === 'rejected' ? 'danger' : 'neutral')"
                                    variant="soft" class="text-xs"
                                >{{ $app->response_type }}</x-ui.badge>
                            @else
                                <span class="text-xs text-muted-foreground">—</span>
                            @endif
                        </x-ui.table-cell>
                    </x-ui.table-row>
                    @endforeach
                </x-ui.table-body>
            </x-ui.table>
        </x-ui.card-content>
    </x-ui.card>
    @endif

    @if($this->approvedLetters->isEmpty() && $this->sentApplications->isEmpty())
    <x-ui.card>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <x-ui.icon name="tabler.send" class="text-muted-foreground mb-3 h-10 w-10" />
            <p class="text-muted-foreground text-sm">{{ __('No applications to send. Approve messages in the Review stage first.') }}</p>
        </div>
    </x-ui.card>
    @endif
</div>
