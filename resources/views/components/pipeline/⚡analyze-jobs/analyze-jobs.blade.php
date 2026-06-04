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
                <tr class="cursor-pointer hover:bg-base-200 transition-colors" wire:click="showJob({{ $loop->index }})">
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



    {{-- Job details modal --}}
    <x-modal wire:model="showJobModal" title=" " box-class="!max-w-3xl !w-full">
        @if($showJobModal && $this->analyzedJobs->isNotEmpty())
        @php $job = $this->analyzedJobs->get($selectedJobIndex); @endphp
        @if($job)
        <div class="space-y-5">
            <div>
                <h3 class="text-lg font-bold">{{ $job->title }}</h3>
                <p class="text-sm text-zinc-500">{{ $job->detail?->company_name ?? $job->company_name }}@if($job->location) · {{ $job->location }}@endif</p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="space-y-3">
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Keyword') }}</span>
                        <p><span class="badge badge-sm badge-ghost">{{ $job->keyword->keyword }}</span></p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</span>
                        <p><span class="badge badge-sm badge-soft
                            {{ $job->status === 'new' ? 'badge-success' : '' }}
                            {{ $job->status === 'processed' ? 'badge-info' : '' }}
                            {{ $job->status === 're_fetched' ? 'badge-warning' : '' }}
                            {{ $job->status === 'failed' ? 'badge-error' : '' }}">
                                {{ $job->status }}
                            </span></p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Seniority') }}</span>
                        <p>{{ $job->detail?->seniority ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Work Type') }}</span>
                        <p>{{ $job->detail?->work_type ?? '—' }}</p>
                    </div>
                    @if($job->detail?->salary_from || $job->detail?->salary_to)
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Salary') }}</span>
                        <p class="font-medium">
                            @if($job->detail->salary_from && $job->detail->salary_to)
                            {{ number_format($job->detail->salary_from) }}–{{ number_format($job->detail->salary_to) }} {{ $job->detail->salary_currency }}
                            @elseif($job->detail->salary_from)
                            {{ __('From') }} {{ number_format($job->detail->salary_from) }} {{ $job->detail->salary_currency }}
                            @else
                            {{ __('Up to') }} {{ number_format($job->detail->salary_to) }} {{ $job->detail->salary_currency }}
                            @endif
                        </p>
                    </div>
                    @endif
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Fetched') }}</span>
                        <p>{{ $job->fetch_count }}x · {{ $job->first_seen_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @if($job->detail?->publication_date)
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Published') }}</span>
                        <p>{{ $job->detail->publication_date instanceof \Carbon\Carbon ? $job->detail->publication_date->diffForHumans() : $job->detail->publication_date }}</p>
                    </div>
                    @endif
                    @if($job->detail?->reposted)
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Repost') }}</span>
                        <p>
                            <span class="badge badge-sm badge-warning gap-1">
                                <x-icon name="tabler.refresh" class="w-3 h-3" />
                                {{ $job->detail->repost_count }}x
                            </span>
                            @if($job->detail->reposted_after_days)
                            <span class="text-xs text-zinc-400">({{ __('after :days days', ['days' => $job->detail->reposted_after_days]) }})</span>
                            @endif
                        </p>
                    </div>
                    @endif
                    @if($job->detail?->contact_email)
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Contact Email') }}</span>
                        <p class="truncate">{{ $job->detail->contact_email }}</p>
                    </div>
                    @endif
                    @if($job->detail?->recruiter_name)
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Recruiter') }}</span>
                        <p>{{ $job->detail->recruiter_name }}@if($job->detail?->phone) · {{ $job->detail->phone }}@endif</p>
                    </div>
                    @endif
                    <div>
                        <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Job URL') }}</span>
                        <p class="truncate">
                            <a href="{{ $job->job_url }}" target="_blank" class="link link-primary link-xs">{{ $job->job_url }}</a>
                        </p>
                    </div>
                </div>
            </div>

            @if($job->detail?->technologies)
            <div>
                <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Technologies') }}</span>
                <div class="flex flex-wrap gap-1 mt-1">
                    @foreach(collect($job->detail->technologies) as $tech)
                    <span class="badge badge-sm badge-ghost">{{ $tech }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($job->detail?->requirements)
            <div>
                <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Requirements') }}</span>
                <p class="text-sm mt-1 whitespace-pre-wrap line-clamp-6">{{ $job->detail->requirements }}</p>
            </div>
            @endif

            @if($job->detail?->responsibilities)
            <div>
                <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Responsibilities') }}</span>
                <p class="text-sm mt-1 whitespace-pre-wrap line-clamp-6">{{ $job->detail->responsibilities }}</p>
            </div>
            @endif

            @if($job->detail?->full_description)
            <div>
                <span class="text-xs text-zinc-400 uppercase tracking-wider">{{ __('Full Description') }}</span>
                <div class="text-sm mt-1 whitespace-pre-wrap line-clamp-8 text-zinc-600 dark:text-zinc-400">{{ $job->detail->full_description }}</div>
            </div>
            @endif

            @if($job->detail?->similarity_score !== null)
            <div class="text-xs text-zinc-400">
                {{ __('Similarity score') }}: {{ round($job->detail->similarity_score * 100) }}%
                @if($job->detail?->similarity_hash)
                · {{ __('Hash') }}: <code class="text-xs">{{ $job->detail->similarity_hash }}</code>
                @endif
            </div>
            @endif
        </div>
        @endif
        @endif

        <x-slot:actions>
            <div class="flex items-center justify-between w-full">
                <x-button icon="o-chevron-left" @click="$wire.prevJob()" :disabled="$selectedJobIndex === 0" label="{{ __('Previous') }}" />
                <span class="text-xs text-zinc-400">
                    {{ $showJobModal && $this->analyzedJobs->isNotEmpty()
                        ? __(':current of :total', ['current' => $selectedJobIndex + 1, 'total' => $this->analyzedJobs->count()])
                        : '' }}
                </span>
                <x-button icon="o-chevron-right" @click="$wire.nextJob()" :disabled="$selectedJobIndex >= $this->analyzedJobs->count() - 1" label="{{ __('Next') }}" />
            </div>
        </x-slot:actions>
    </x-modal>
    @else
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-8 text-center">
        <x-icon name="tabler.search" class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
        <p class="text-sm text-zinc-500">{{ __('No analyzed jobs yet. Fetch jobs first, then analyze them.') }}</p>
    </div>
    @endif
</div>
