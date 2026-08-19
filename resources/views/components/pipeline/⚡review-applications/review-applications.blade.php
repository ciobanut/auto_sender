<div class="space-y-4">
    <div>
        <h3 class="font-semibold">{{ __('Review Applications') }}</h3>
        <p class="text-xs text-muted-foreground">{{ __('Review AI-generated messages, edit if needed, and approve for sending.') }}</p>
    </div>

    @if($this->pendingLetters->isEmpty())
    <x-ui.card>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <x-ui.icon name="tabler.eye" class="text-muted-foreground mb-3 h-10 w-10" />
            <p class="text-muted-foreground text-sm">{{ __('No pending reviews. Generate AI messages first.') }}</p>
        </div>
    </x-ui.card>
    @else
    <div class="grid gap-4 lg:grid-cols-2">
        {{-- Queue list --}}
        <div class="space-y-2">
            <h4 class="text-sm font-medium text-muted-foreground">{{ __('Pending Review') }} ({{ $this->pendingLetters->count() }})</h4>
            @foreach($this->pendingLetters as $letter)
            <div wire:click="select({{ $letter->id }})" class="cursor-pointer rounded-xl border p-4 transition-colors
                        {{ $selectedLetterId === $letter->id ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'bg-card hover:border-primary/50' }}">
                <div class="flex items-start justify-between mb-1">
                    <h5 class="font-medium text-sm">{{ $letter->jobLink?->title }}</h5>
                    <x-ui.badge variant="soft" class="text-xs">{{ $letter->keyword?->keyword }}</x-ui.badge>
                </div>
                <p class="text-xs text-muted-foreground">{{ $letter->jobLink?->company_name }}</p>
                @if($letter->ai_confidence_score)
                <div class="mt-2">
                    <div class="w-full bg-muted rounded-full h-1.5">
                        <div class="bg-primary h-1.5 rounded-full" style="width: {{ $letter->ai_confidence_score * 100 }}%"></div>
                    </div>
                    <p class="text-xs text-muted-foreground mt-0.5">{{ __('Match') }}: {{ round($letter->ai_confidence_score * 100) }}%</p>
                </div>
                @endif
                <div class="flex items-center gap-2 mt-3">
                    <x-ui.button variant="ghost" size="xs" wire:click.stop="approve({{ $letter->id }})" wire:loading.attr="disabled">
                        <x-ui.icon name="tabler.check" class="h-3.5 w-3.5 text-success" /> {{ __('Approve') }}
                    </x-ui.button>
                    <x-ui.button variant="ghost" size="xs" wire:click.stop="reject({{ $letter->id }})" wire:loading.attr="disabled">
                        <x-ui.icon name="tabler.x" class="h-3.5 w-3.5 text-destructive" /> {{ __('Reject') }}
                    </x-ui.button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Editor --}}
        <x-ui.card variant="sectioned" class="sticky top-20 self-start">
            @if($this->selectedLetter)
                <x-ui.card-header>
                    <x-ui.card-title>{{ __('Edit Message') }}</x-ui.card-title>
                    <x-ui.card-description>{{ $this->selectedLetter->jobLink?->title }} · {{ $this->selectedLetter->jobLink?->company_name }}</x-ui.card-description>
                </x-ui.card-header>
                <x-ui.card-content>
                    <x-ui.textarea wire:model="editedContent" rows="12" class="font-mono text-sm" />
                </x-ui.card-content>
                <div class="px-6 pb-6">
                    <div class="flex items-center gap-2">
                        <x-ui.button variant="default" size="sm" wire:click="saveEdit">
                            <x-ui.icon name="tabler.device-floppy" class="h-4 w-4" /> {{ __('Save Edit') }}
                        </x-ui.button>
                        <x-ui.button size="sm" wire:click="approve({{ $this->selectedLetter->id }})">
                            <x-ui.icon name="tabler.check" class="h-4 w-4" /> {{ __('Approve & Close') }}
                        </x-ui.button>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center h-full text-sm text-muted-foreground py-12">
                    {{ __('Select a message to review') }}
                </div>
            @endif
        </x-ui.card>
    </div>
    @endif
</div>
