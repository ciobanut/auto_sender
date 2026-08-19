<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold tracking-tight">{{ __('Fetch Jobs') }}</h3>
            <p class="text-muted-foreground text-sm">{{ __('Scrape Rabota.md for new job listings matching your keywords.') }}</p>
        </div>
        <x-ui.button wire:click="fetch" wire:loading.attr="disabled" :disabled="$this->keywords->isEmpty()">
            <x-ui.icon name="tabler.download" class="h-4 w-4" wire:loading.remove />
            <span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
            {{ __('Fetch Jobs') }}
        </x-ui.button>
    </div>

    {{-- Empty states --}}
    @if($this->keywords->isEmpty())
        <x-ui.card>
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <x-ui.icon name="tabler.category" class="text-muted-foreground mb-3 h-10 w-10" />
                <p class="text-muted-foreground text-sm">{{ __('Add active keywords first in Job Categories.') }}</p>
            </div>
        </x-ui.card>
    @elseif($this->jobLinks->isEmpty())
        <x-ui.card>
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <x-ui.icon name="tabler.search" class="text-muted-foreground mb-3 h-10 w-10" />
                <p class="text-muted-foreground text-sm">{{ __('No jobs fetched yet. Click "Fetch Jobs" to start.') }}</p>
            </div>
        </x-ui.card>
    @else
        {{-- Stats row --}}
        @php
            $total = $this->jobLinks->count();
            $new = $this->jobLinks->where('status', 'new')->count();
            $processed = $this->jobLinks->where('status', 'processed')->count();
        @endphp

        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.card>
                <span class="text-muted-foreground text-sm">{{ __('Total Jobs') }}</span>
                <div class="mt-2 text-2xl font-bold tracking-tight">{{ $total }}</div>
            </x-ui.card>
            <x-ui.card>
                <span class="text-muted-foreground text-sm">{{ __('New') }}</span>
                <div class="mt-2 text-2xl font-bold tracking-tight text-success">{{ $new }}</div>
            </x-ui.card>
            <x-ui.card>
                <span class="text-muted-foreground text-sm">{{ __('Processed') }}</span>
                <div class="mt-2 text-2xl font-bold tracking-tight text-info">{{ $processed }}</div>
            </x-ui.card>
        </div>

        {{-- Jobs table --}}
        <x-ui.card variant="sectioned">
            <x-ui.card-header class="flex-row items-center justify-between">
                <div>
                    <x-ui.card-title>{{ __('Fetched Jobs') }}</x-ui.card-title>
                    <x-ui.card-description>{{ __('Latest 50 job listings') }}</x-ui.card-description>
                </div>
            </x-ui.card-header>
            <x-ui.card-content>
                <x-ui.table>
                    <x-ui.table-header>
                        <x-ui.table-row>
                            <x-ui.table-head>{{ __('Job Title') }}</x-ui.table-head>
                            <x-ui.table-head>{{ __('Company') }}</x-ui.table-head>
                            <x-ui.table-head>{{ __('Keyword') }}</x-ui.table-head>
                            <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                            <x-ui.table-head class="hidden sm:table-cell">{{ __('First Seen') }}</x-ui.table-head>
                            <x-ui.table-head class="text-right">{{ __('Fetched') }}</x-ui.table-head>
                        </x-ui.table-row>
                    </x-ui.table-header>
                    <x-ui.table-body>
                        @foreach($this->jobLinks as $link)
                            <x-ui.table-row>
                                <x-ui.table-cell class="font-medium max-w-xs truncate">{{ $link->title }}</x-ui.table-cell>
                                <x-ui.table-cell>{{ $link->company_name }}</x-ui.table-cell>
                                <x-ui.table-cell>
                                    <x-ui.badge variant="soft" class="text-xs">{{ $link->keyword->keyword }}</x-ui.badge>
                                </x-ui.table-cell>
                                <x-ui.table-cell>
                                    <x-ui.badge
                                        :tone="$link->status === 'new' ? 'success' : ($link->status === 're_fetched' ? 'warning' : ($link->status === 'processed' ? 'info' : 'neutral'))"
                                        variant="soft"
                                    >{{ $link->status }}</x-ui.badge>
                                </x-ui.table-cell>
                                <x-ui.table-cell class="text-muted-foreground hidden sm:table-cell">{{ $link->first_seen_at->diffForHumans() }}</x-ui.table-cell>
                                <x-ui.table-cell class="text-right">{{ $link->fetch_count }}x</x-ui.table-cell>
                            </x-ui.table-row>
                        @endforeach
                    </x-ui.table-body>
                </x-ui.table>
            </x-ui.card-content>
        </x-ui.card>
    @endif
</div>
