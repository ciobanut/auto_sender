<div class="space-y-4" @if($isGenerating) wire:poll.5s="pollGenerate" @endif>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-semibold">{{ __('Generate AI Messages') }}</h3>
            <p class="text-xs text-muted-foreground">{{ __('AI creates personalized cover letters for each analyzed job.') }}</p>
        </div>
        <x-ui.button wire:click="generate" wire:loading.attr="disabled" :disabled="$this->pendingJobs->isEmpty()">
            <x-ui.icon name="tabler.messages" class="h-4 w-4" wire:loading.remove />
            <span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
            {{ __('Generate Messages') }}
        </x-ui.button>
    </div>

    {{-- Pending generation --}}
    @if($this->pendingJobs->isNotEmpty())
    <x-ui.card class="border-warning/50 bg-warning/5">
        <div class="flex items-center gap-2 mb-3">
            <x-ui.icon name="tabler.alert-circle" class="h-4 w-4 text-warning" />
            <h4 class="text-sm font-medium">{{ __('Jobs awaiting AI messages') }} ({{ $this->pendingJobs->count() }})</h4>
        </div>
        <div class="space-y-1 text-sm">
            @foreach($this->pendingJobs as $job)
            <div class="flex items-center justify-between py-0.5">
                <span class="truncate">{{ $job->title }}</span>
                <span class="text-muted-foreground text-xs shrink-0 ml-4">{{ $job->keyword->keyword }}</span>
            </div>
            @endforeach
        </div>
    </x-ui.card>
    @endif

    {{-- Generated drafts --}}
    @if($this->generatedDrafts->isNotEmpty())
    <div class="grid gap-3">
        @foreach($this->generatedDrafts as $letter)
        <x-ui.card>
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h4 class="font-medium text-sm">{{ $letter->jobLink?->title }}</h4>
                    <p class="text-xs text-muted-foreground">{{ $letter->jobLink?->company_name }}</p>
                </div>
                <x-ui.badge :tone="$letter->status === 'draft' ? 'warning' : 'success'" variant="soft" class="text-xs">{{ $letter->status }}</x-ui.badge>
            </div>
            @if($letter->match_explanation)
            <p class="text-xs text-muted-foreground line-clamp-2">{{ $letter->match_explanation }}</p>
            @endif
        </x-ui.card>
        @endforeach
    </div>
    @else
    <x-ui.card>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <x-ui.icon name="tabler.messages" class="text-muted-foreground mb-3 h-10 w-10" />
            <p class="text-muted-foreground text-sm">{{ __('No generated messages yet. Analyze jobs first, then generate.') }}</p>
        </div>
    </x-ui.card>
    @endif
</div>
