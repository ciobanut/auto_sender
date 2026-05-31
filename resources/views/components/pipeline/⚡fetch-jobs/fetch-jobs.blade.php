<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-semibold">{{ __('Fetch Jobs') }}</h3>
            <p class="text-xs text-zinc-500">{{ __('Scrape Rabota.md for new job listings matching your keywords.') }}</p>
        </div>
        <x-button variant="primary" wire:click="fetch" wire:loading.attr="disabled" :disabled="$this->keywords->isEmpty()">
            @if($isFetching)
            <span class="loading loading-spinner loading-sm"></span>
            @else
            <x-icon name="tabler.download" class="w-4 h-4" />
            @endif
            {{ __('Fetch Jobs') }}
        </x-button>
    </div>

    @if($this->keywords->isEmpty())
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-8 text-center">
        <x-icon name="tabler.category" class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
        <p class="text-sm text-zinc-500">{{ __('Add active keywords first in Job Categories.') }}</p>
    </div>
    @elseif($this->jobLinks->isEmpty())
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-8 text-center">
        <x-icon name="tabler.search" class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
        <p class="text-sm text-zinc-500">{{ __('No jobs fetched yet. Click "Fetch Jobs" to start.') }}</p>
    </div>
    @else
    <div class="bg-base-100 rounded-xl border border-base-content/5 overflow-hidden">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>{{ __('Job Title') }}</th>
                    <th>{{ __('Company') }}</th>
                    <th>{{ __('Keyword') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('First Seen') }}</th>
                    <th>{{ __('Fetched') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->jobLinks as $link)
                <tr>
                    <td class="font-medium max-w-xs truncate">{{ $link->title }}</td>
                    <td class="text-sm">{{ $link->company_name }}</td>
                    <td><span class="badge badge-sm badge-ghost">{{ $link->keyword->keyword }}</span></td>
                    <td>
                        <span class="badge badge-sm
                                    {{ $link->status === 'new' ? 'badge-success' : '' }}
                                    {{ $link->status === 're_fetched' ? 'badge-warning' : '' }}
                                    {{ $link->status === 'processed' ? 'badge-info' : '' }}
                                    {{ $link->status === 'ignored' ? 'badge-ghost' : '' }}">
                            {{ $link->status }}
                        </span>
                    </td>
                    <td class="text-sm text-zinc-500">{{ $link->first_seen_at->diffForHumans() }}</td>
                    <td class="text-sm text-zinc-500">{{ $link->fetch_count }}x</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
