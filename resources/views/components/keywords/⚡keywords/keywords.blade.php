<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('Job Categories') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('Manage keywords, CVs, and AI instructions per category.') }}</p>
        </div>
        <x-ui.button wire:click="create">
            <x-ui.icon name="tabler.plus" class="h-4 w-4" /> {{ __('Add Keyword') }}
        </x-ui.button>
    </div>

    @if($this->keywords->isEmpty())
    <x-ui.card>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <x-ui.icon name="tabler.category" class="text-muted-foreground mb-3 h-12 w-12" />
            <h3 class="text-lg font-medium mb-2">{{ __('No keywords yet') }}</h3>
            <p class="text-muted-foreground text-sm mb-4">{{ __('Add your first job keyword to start fetching opportunities.') }}</p>
            <x-ui.button wire:click="create">
                <x-ui.icon name="tabler.plus" class="h-4 w-4" /> {{ __('Add Keyword') }}
            </x-ui.button>
        </div>
    </x-ui.card>
    @else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($this->keywords as $keyword)
        <x-ui.card class="relative group">
            {{-- Sort buttons --}}
            <div class="absolute right-3 top-3 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <x-ui.button variant="ghost" size="icon-xs" wire:click="moveUp({{ $keyword->id }})">
                    <x-ui.icon name="tabler.chevron-up" class="h-3.5 w-3.5" />
                </x-ui.button>
                <x-ui.button variant="ghost" size="icon-xs" wire:click="moveDown({{ $keyword->id }})">
                    <x-ui.icon name="tabler.chevron-down" class="h-3.5 w-3.5" />
                </x-ui.button>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between mb-3 pr-16">
                <span class="font-semibold text-lg">{{ $keyword->keyword }}</span>
                <button wire:click="toggleActive({{ $keyword->id }})" class="cursor-pointer">
                    <x-ui.badge :tone="$keyword->is_active ? 'success' : 'neutral'" variant="soft">
                        {{ $keyword->is_active ? __('Active') : __('Inactive') }}
                    </x-ui.badge>
                </button>
            </div>

            {{-- Details --}}
            <div class="space-y-2 text-sm text-muted-foreground">
                <div class="flex items-center gap-2">
                    <x-ui.icon name="tabler.file-text" class="h-4 w-4 shrink-0" />
                    <span class="truncate">{{ $keyword->cv_path ? basename($keyword->cv_path) : __('No CV') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.icon name="tabler.robot" class="h-4 w-4 shrink-0" />
                    <span class="truncate">{{ $keyword->ai_instructions ? Str::limit($keyword->ai_instructions, 40) : __('Default instructions') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.icon name="tabler.clock" class="h-4 w-4 shrink-0" />
                    <span>{{ __('Cooldown') }}: {{ $keyword->cooldown_hours }}h</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 mt-4 pt-3 border-t border-border">
                <x-ui.button variant="ghost" size="xs" wire:click="edit({{ $keyword->id }})">
                    <x-ui.icon name="tabler.pencil" class="h-3.5 w-3.5" /> {{ __('Edit') }}
                </x-ui.button>
                <x-ui.button variant="ghost" size="xs" class="text-destructive" wire:click="delete({{ $keyword->id }})" wire:confirm="{{ __('Are you sure?') }}">
                    <x-ui.icon name="tabler.trash" class="h-3.5 w-3.5" /> {{ __('Delete') }}
                </x-ui.button>
            </div>
        </x-ui.card>
        @endforeach
    </div>
    @endif

    {{-- Add/Edit Modal --}}
    <x-modal wire:model="showForm" title="{{ $editingKeywordId ? __('Edit Keyword') : __('Add Keyword') }}">
        <div class="space-y-4">
            <x-ui.input wire:model="keyword" :label="__('Keyword')" placeholder="e.g. PHP, Laravel, React" />

            <div>
                <label class="text-sm font-medium mb-1 block">{{ __('CV (PDF, DOCX, or TXT)') }}</label>
                <input type="file" wire:model="cv" accept=".pdf,.docx,.txt" class="w-full text-sm rounded-md border border-input bg-background px-3 py-2 file:mr-3 file:rounded-md file:border-0 file:bg-primary file:text-primary-foreground file:text-sm file:font-medium file:px-3 file:py-1 hover:file:bg-primary/90" />
                @error('cv') <p class="text-xs text-destructive mt-1">{{ $message }}</p> @enderror
            </div>

            <x-ui.textarea wire:model="ai_instructions" :label="__('AI Instructions')" placeholder="{{ __('Custom instructions for cover letter generation...') }}" rows="3" />

            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="auto_apply_enabled" class="rounded border-input" />
                    <span class="text-sm">{{ __('Auto-apply') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="rounded border-input" />
                    <span class="text-sm">{{ __('Active') }}</span>
                </label>
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block">{{ __('Cooldown (hours)') }}</label>
                <input type="range" wire:model="cooldown_hours" min="1" max="2160" class="w-full accent-primary" />
                <div class="flex justify-between text-xs text-muted-foreground mt-1">
                    <span>1h</span>
                    <span>{{ $cooldown_hours }}h {{ __('(~ '.round($cooldown_hours / 24).' days)') }}</span>
                    <span>2160h</span>
                </div>
            </div>
        </div>

        <x-slot:actions>
            <x-ui.button variant="ghost" wire:click="$set('showForm', false)">{{ __('Cancel') }}</x-ui.button>
            <x-ui.button wire:click="save">{{ __('Save') }}</x-ui.button>
        </x-slot:actions>
    </x-modal>
</div>
