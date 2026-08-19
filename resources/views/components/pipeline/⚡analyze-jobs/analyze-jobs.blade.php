<div class="space-y-4" @if($isAnalyzing) wire:poll.5s="pollAnalyze" @endif>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-semibold">{{ __('Analyze Jobs') }}</h3>
            <p class="text-xs text-muted-foreground">{{ __('Extract full details, detect reposts, and classify job opportunities.') }}</p>
        </div>
        <x-ui.button wire:click="analyze" wire:loading.attr="disabled" :disabled="$this->pendingJobs->isEmpty()">
            <x-ui.icon name="tabler.search" class="h-4 w-4" wire:loading.remove />
            <span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
            {{ __('Analyze Jobs') }}
        </x-ui.button>
    </div>

    {{-- Pending analysis --}}
    @if($this->pendingJobs->isNotEmpty())
    <x-ui.card class="border-warning/50 bg-warning/5">
        <div class="flex items-center gap-2 mb-3">
            <x-ui.icon name="tabler.alert-circle" class="h-4 w-4 text-warning" />
            <h4 class="text-sm font-medium">{{ __('Jobs awaiting analysis') }} ({{ $this->pendingJobs->count() }})</h4>
        </div>
        <div class="space-y-1 text-sm">
            @foreach($this->pendingJobs as $job)
            <div class="flex items-center justify-between py-0.5">
                <span class="truncate">{{ $job->title }}</span>
                <span class="text-muted-foreground text-xs shrink-0 ml-4">{{ $job->company_name }}</span>
            </div>
            @endforeach
        </div>
    </x-ui.card>
    @endif

    {{-- Analyzed jobs --}}
    @if($this->analyzedJobs->isNotEmpty())
    <x-ui.card variant="sectioned">
        <x-ui.card-header>
            <x-ui.card-title>{{ __('Analyzed Jobs') }} ({{ $this->analyzedJobs->count() }})</x-ui.card-title>
        </x-ui.card-header>
        <x-ui.card-content>
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>{{ __('Job Title') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Company') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Tech Stack') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Match') }}</x-ui.table-head>
                        <x-ui.table-head class="w-10"></x-ui.table-head>
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @foreach($this->analyzedJobs as $index => $job)
                    <x-ui.table-row wire:click="openJob({{ $index }})" class="cursor-pointer">
                        <x-ui.table-cell class="font-medium max-w-xs truncate">{{ $job->title }}</x-ui.table-cell>
                        <x-ui.table-cell>{{ $job->detail?->company_name ?? $job->company_name }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            @if($job->detail?->technologies)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($job->detail->technologies, 0, 3) as $tech)
                                        <x-ui.badge variant="soft" class="text-xs">{{ $tech }}</x-ui.badge>
                                    @endforeach
                                    @if(count($job->detail->technologies) > 3)
                                        <x-ui.badge variant="soft" class="text-xs">+{{ count($job->detail->technologies) - 3 }}</x-ui.badge>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted-foreground">—</span>
                            @endif
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            @if($job->detail?->similarity_score !== null)
                                @php $score = round($job->detail->similarity_score * 100); @endphp
                                <x-ui.badge :tone="$score >= 70 ? 'success' : ($score >= 40 ? 'warning' : 'neutral')" variant="soft" class="text-xs">{{ $score }}%</x-ui.badge>
                            @else
                                <span class="text-muted-foreground">—</span>
                            @endif
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <x-ui.icon name="tabler.chevron-right" class="h-4 w-4 text-muted-foreground" />
                        </x-ui.table-cell>
                    </x-ui.table-row>
                    @endforeach
                </x-ui.table-body>
            </x-ui.table>
        </x-ui.card-content>
    </x-ui.card>

    {{-- Job details modal --}}
    <x-modal wire:model="showJobModal" title=" " box-class="!max-w-6xl !w-full">
        @if($showJobModal && $this->analyzedJobs->isNotEmpty())
        @php $job = $this->analyzedJobs->get($selectedJobIndex); @endphp
        @if($job)
        <div class="space-y-5">
            <div>
                <h3 class="text-lg font-bold">{{ $job->title }}</h3>
                <p class="text-sm text-muted-foreground">{{ $job->detail?->company_name ?? $job->company_name }}@if($job->location) · {{ $job->location }}@endif</p>
            </div>

            @if($job->detail?->technologies)
            <div>
                <span class="text-xs text-muted-foreground uppercase tracking-wider">{{ __('Technologies') }}</span>
                <div class="flex flex-wrap gap-1 mt-1">
                    @foreach(collect($job->detail->technologies) as $tech)
                    <x-ui.badge variant="soft" class="text-xs">{{ $tech }}</x-ui.badge>
                    @endforeach
                </div>
            </div>
            @endif

            @if($job->detail?->full_description)
            <div>
                <span class="text-xs text-muted-foreground uppercase tracking-wider">{{ __('Full Description') }}</span>
                <div class="text-sm mt-1 prose prose-sm max-w-none dark:prose-invert">{!! $job->detail->full_description !!}</div>
            </div>
            @endif

            @if($job->detail?->similarity_score !== null)
            <div class="text-xs text-muted-foreground">
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
                <x-ui.button variant="outline" @click="$wire.prevJob()" :disabled="$selectedJobIndex === 0">
                    <x-ui.icon name="tabler.chevron-left" class="h-4 w-4" /> {{ __('Previous') }}
                </x-ui.button>
                <span class="text-xs text-muted-foreground">
                    {{ $showJobModal && $this->analyzedJobs->isNotEmpty()
                        ? __(':current of :total', ['current' => $selectedJobIndex + 1, 'total' => $this->analyzedJobs->count()])
                        : '' }}
                </span>
                <x-ui.button variant="outline" @click="$wire.nextJob()" :disabled="$selectedJobIndex >= $this->analyzedJobs->count() - 1">
                    {{ __('Next') }} <x-ui.icon name="tabler.chevron-right" class="h-4 w-4" />
                </x-ui.button>
            </div>
        </x-slot:actions>
    </x-modal>
    @else
    <x-ui.card>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <x-ui.icon name="tabler.search" class="text-muted-foreground mb-3 h-10 w-10" />
            <p class="text-muted-foreground text-sm">{{ __('No analyzed jobs yet. Fetch jobs first, then analyze them.') }}</p>
        </div>
    </x-ui.card>
    @endif
</div>
