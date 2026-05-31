<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-semibold">{{ __('Generate AI Messages') }}</h3>
            <p class="text-xs text-zinc-500">{{ __('AI creates personalized cover letters for each analyzed job.') }}</p>
        </div>
        <x-button variant="primary" wire:click="generate" wire:loading.attr="disabled" :disabled="$this->pendingJobs->isEmpty()">
            @if($isGenerating)
            <span class="loading loading-spinner loading-sm"></span>
            @else
            <x-icon name="tabler.messages" class="w-4 h-4" />
            @endif
            {{ __('Generate Messages') }}
        </x-button>
    </div>

    {{-- Pending generation --}}
    @if($this->pendingJobs->isNotEmpty())
    <div class="bg-base-100 rounded-xl border border-amber-200 dark:border-amber-800 p-4">
        <div class="flex items-center gap-2 mb-3">
            <x-icon name="tabler.alert-circle" class="w-4 h-4 text-amber-500" />
            <h4 class="text-sm font-medium">{{ __('Jobs awaiting AI messages') }} ({{ $this->pendingJobs->count() }})</h4>
        </div>
        <div class="space-y-1 text-sm">
            @foreach($this->pendingJobs as $job)
            <div class="flex items-center justify-between py-0.5">
                <span class="truncate">{{ $job->title }}</span>
                <span class="text-zinc-500 text-xs shrink-0 ml-4">{{ $job->keyword->keyword }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Generated drafts --}}
    @if($this->generatedDrafts->isNotEmpty())
    <div class="grid gap-3">
        @foreach($this->generatedDrafts as $letter)
        <div class="bg-base-100 rounded-xl border border-base-content/5 p-4">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h4 class="font-medium text-sm">{{ $letter->jobLink?->title }}</h4>
                    <p class="text-xs text-zinc-500">{{ $letter->jobLink?->company_name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($letter->is_follow_up)
                    <span class="badge badge-sm badge-warning">{{ __('Follow-up') }}</span>
                    @endif
                    <span class="badge badge-sm
                                {{ $letter->status === 'draft' ? 'badge-ghost' : '' }}
                                {{ $letter->status === 'approved' ? 'badge-success' : '' }}
                                {{ $letter->status === 'edited' ? 'badge-info' : '' }}">
                        {{ $letter->status }}
                    </span>
                </div>
            </div>
            @if($letter->ai_confidence_score)
            <div class="flex items-center gap-2 text-xs text-zinc-500">
                <span>{{ __('Confidence') }}: {{ round($letter->ai_confidence_score * 100) }}%</span>
                @if($letter->match_explanation)
                <span>· {{ $letter->match_explanation }}</span>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-8 text-center">
        <x-icon name="tabler.messages" class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
        <p class="text-sm text-zinc-500">{{ __('No generated messages yet. Analyze jobs first, then generate.') }}</p>
    </div>
    @endif
</div>
