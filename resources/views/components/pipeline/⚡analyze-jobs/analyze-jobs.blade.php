<div class="space-y-4" @if($isAnalyzing) wire:poll.5s="pollAnalyze" @endif>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-semibold">{{ __('Analyze Jobs') }}</h3>
            <p class="text-xs text-zinc-500">{{ __('Extract full details, detect reposts, and classify job opportunities.') }}</p>
        </div>
        <x-button variant="primary" wire:click="analyze" wire:loading.attr="disabled" :disabled="$this->pendingJobs->isEmpty()">
            @if($isAnalyzing)
            <span class="loading loading-spinner loading-sm"></span>
            @else
            <x-icon name="tabler.search" class="w-4 h-4" />
            @endif
            {{ __('Analyze New Jobs') }}
        </x-button>
    </div>

    {{-- Pending analysis --}}
    @if($this->pendingJobs->isNotEmpty())
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-4">
        <h4 class="text-sm font-medium mb-3">{{ __('Pending Analysis') }} ({{ $this->pendingJobs->count() }})</h4>
        <div class="space-y-2">
            @foreach($this->pendingJobs as $job)
            <div class="flex items-center justify-between text-sm py-1">
                <span class="font-medium truncate">{{ $job->title }}</span>
                <span class="text-zinc-500 shrink-0 ml-4">{{ $job->company_name }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Analyzed jobs --}}
    @if($this->analyzedJobs->isNotEmpty())
    <div class="bg-base-100 rounded-xl border border-base-content/5 overflow-hidden">
        <div class="px-4 py-3 border-b border-base-content/5">
            <h4 class="text-sm font-medium">{{ __('Analyzed Jobs') }} ({{ $this->analyzedJobs->count() }})</h4>
        </div>
        <table class="table w-full">
            <thead>
                <tr>
                    <th>{{ __('Job Title') }}</th>
                    <th>{{ __('Company') }}</th>
                    <th>{{ __('Tech Stack') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Repost') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->analyzedJobs as $job)
                <tr>
                    <td class="font-medium max-w-xs truncate">{{ $job->title }}</td>
                    <td class="text-sm">{{ $job->detail?->company_name ?? $job->company_name }}</td>
                    <td>
                        @if($job->detail?->technologies)
                        <div class="flex flex-wrap gap-1">
                            @foreach(collect($job->detail->technologies)->take(3) as $tech)
                            <span class="badge badge-sm badge-ghost">{{ $tech }}</span>
                            @endforeach
                        </div>
                        @else
                        <span class="text-xs text-zinc-400">—</span>
                        @endif
                    </td>
                    <td class="text-sm">{{ $job->detail?->work_type ?? '—' }}</td>
                    <td>
                        @if($job->detail?->reposted)
                        <span class="badge badge-sm badge-warning gap-1">
                            <x-icon name="tabler.refresh" class="w-3 h-3" />
                            {{ $job->detail->repost_count }}x
                        </span>
                        @else
                        <span class="text-xs text-zinc-400">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-8 text-center">
        <x-icon name="tabler.search" class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
        <p class="text-sm text-zinc-500">{{ __('No analyzed jobs yet. Fetch jobs first, then analyze them.') }}</p>
    </div>
    @endif
</div>
