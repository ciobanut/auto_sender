<div class="space-y-4">
    <div>
        <h3 class="font-semibold">{{ __('Review Applications') }}</h3>
        <p class="text-xs text-zinc-500 mt-1">{{ __('Review AI-generated messages, edit if needed, and approve for sending.') }}</p>
    </div>

    @if($this->pendingLetters->isEmpty())
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center">
            <x-icon name="tabler.eye" class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
            <p class="text-sm text-zinc-500">{{ __('No pending reviews. Generate AI messages first.') }}</p>
        </div>
    @else
        <div class="grid gap-4 lg:grid-cols-2">
            {{-- Queue list --}}
            <div class="space-y-2">
                <h4 class="text-sm font-medium text-zinc-500">{{ __('Pending Review') }} ({{ $this->pendingLetters->count() }})</h4>
                @foreach($this->pendingLetters as $letter)
                    <div wire:click="select({{ $letter->id }})" class="cursor-pointer bg-base-100 rounded-xl border p-4 transition-colors
                        {{ $selectedLetterId === $letter->id ? 'border-primary ring-1 ring-primary' : 'border-zinc-200 dark:border-zinc-700 hover:border-primary/50' }}">
                        <div class="flex items-start justify-between mb-1">
                            <h5 class="font-medium text-sm">{{ $letter->jobLink?->title }}</h5>
                            <span class="badge badge-sm badge-ghost">{{ $letter->keyword?->keyword }}</span>
                        </div>
                        <p class="text-xs text-zinc-500">{{ $letter->jobLink?->company_name }}</p>
                        @if($letter->ai_confidence_score)
                            <div class="mt-2">
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5">
                                    <div class="bg-primary h-1.5 rounded-full" style="width: {{ $letter->ai_confidence_score * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-zinc-500 mt-0.5">{{ __('Match') }}: {{ round($letter->ai_confidence_score * 100) }}%</p>
                            </div>
                        @endif
                        <div class="flex items-center gap-2 mt-3">
                            <x-button class="btn-ghost btn-xs" wire:click="approve({{ $letter->id }})" wire:loading.attr="disabled">
                                <x-icon name="tabler.check" class="w-3.5 h-3.5 text-success" /> {{ __('Approve') }}
                            </x-button>
                            <x-button class="btn-ghost btn-xs" wire:click="reject({{ $letter->id }})" wire:loading.attr="disabled">
                                <x-icon name="tabler.x" class="w-3.5 h-3.5 text-error" /> {{ __('Reject') }}
                            </x-button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Editor --}}
            <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
                @if($selectedLetter)
                    <div class="mb-4">
                        <h4 class="font-medium text-sm">{{ $selectedLetter->jobLink?->title }}</h4>
                        <p class="text-xs text-zinc-500">{{ $selectedLetter->jobLink?->company_name }}</p>
                        @if($selectedLetter->match_explanation)
                            <p class="text-xs text-primary mt-1">{{ $selectedLetter->match_explanation }}</p>
                        @endif
                    </div>

                    <textarea wire:model="editedContent" rows="12" class="textarea textarea-bordered w-full text-sm font-mono"></textarea>

                    <div class="flex items-center gap-2 mt-3">
                        <x-button variant="primary" wire:click="saveEdit" class="btn-sm">
                            <x-icon name="tabler.device-floppy" class="w-4 h-4" /> {{ __('Save Edit') }}
                        </x-button>
                        <x-button class="btn-sm btn-success" wire:click="approve({{ $selectedLetter->id }})">
                            <x-icon name="tabler.check" class="w-4 h-4" /> {{ __('Approve & Close') }}
                        </x-button>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full text-sm text-zinc-400">
                        {{ __('Select a message to review') }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
