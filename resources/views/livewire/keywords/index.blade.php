<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('Job Categories') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Manage keywords, CVs, and AI instructions per category.') }}</p>
        </div>
        <x-button variant="primary" wire:click="create">
            <x-icon name="tabler.plus" class="w-4 h-4" /> {{ __('Add Keyword') }}
        </x-button>
    </div>

    @if($this->keywords->isEmpty())
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
            <x-icon name="tabler.category" class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
            <h3 class="text-lg font-medium mb-2">{{ __('No keywords yet') }}</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">{{ __('Add your first job keyword to start fetching opportunities.') }}</p>
            <x-button variant="primary" wire:click="create">
                <x-icon name="tabler.plus" class="w-4 h-4" /> {{ __('Add Keyword') }}
            </x-button>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($this->keywords as $keyword)
                <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 relative group">
                    {{-- Sort buttons --}}
                    <div class="absolute right-3 top-3 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button wire:click="moveUp({{ $keyword->id }})" class="btn-ghost btn-xs p-1">
                            <x-icon name="tabler.chevron-up" class="w-3.5 h-3.5" />
                        </button>
                        <button wire:click="moveDown({{ $keyword->id }})" class="btn-ghost btn-xs p-1">
                            <x-icon name="tabler.chevron-down" class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-3 pr-16">
                        <span class="font-semibold text-lg">{{ $keyword->keyword }}</span>
                        <button wire:click="toggleActive({{ $keyword->id }})" class="cursor-pointer">
                            <span class="badge badge-sm {{ $keyword->is_active ? 'badge-success' : 'badge-ghost' }}">
                                {{ $keyword->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </button>
                    </div>

                    {{-- Details --}}
                    <div class="space-y-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <div class="flex items-center gap-2">
                            <x-icon name="tabler.file-text" class="w-4 h-4 shrink-0" />
                            <span class="truncate">{{ $keyword->cv_path ? basename($keyword->cv_path) : __('No CV') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-icon name="tabler.robot" class="w-4 h-4 shrink-0" />
                            <span class="truncate">{{ $keyword->ai_instructions ? Str::limit($keyword->ai_instructions, 40) : __('Default instructions') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-icon name="tabler.send" class="w-4 h-4 shrink-0" />
                            <span>{{ $keyword->auto_apply_enabled ? __('Auto-apply on') : __('Auto-apply off') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-icon name="tabler.clock" class="w-4 h-4 shrink-0" />
                            <span>{{ __('Cooldown') }}: {{ $keyword->cooldown_hours }}h</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-700">
                        <x-button class="btn-ghost btn-xs" wire:click="edit({{ $keyword->id }})">
                            <x-icon name="tabler.pencil" class="w-3.5 h-3.5" /> {{ __('Edit') }}
                        </x-button>
                        <x-button class="btn-ghost btn-xs text-error" wire:click="delete({{ $keyword->id }})" wire:confirm="{{ __('Are you sure?') }}">
                            <x-icon name="tabler.trash" class="w-3.5 h-3.5" /> {{ __('Delete') }}
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Add/Edit Modal --}}
    <x-modal wire:model="showForm" title="{{ $editingKeywordId ? __('Edit Keyword') : __('Add Keyword') }}" class="max-w-lg">
        <div class="space-y-4">
            {{-- Keyword name --}}
            <div>
                <x-input wire:model="keyword" :label="__('Keyword')" placeholder="e.g. PHP, Laravel, React" />
            </div>

            {{-- CV upload --}}
            <div>
                <label class="text-sm font-medium mb-1 block">{{ __('CV (PDF, DOCX, or TXT)') }}</label>
                <input type="file" wire:model="cv" accept=".pdf,.docx,.txt" class="file-input file-input-bordered w-full text-sm" />
                @error('cv') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- AI Instructions --}}
            <div>
                <x-textarea wire:model="ai_instructions" :label="__('AI Instructions')" placeholder="{{ __('Custom instructions for cover letter generation...') }}" rows="3" />
            </div>

            {{-- Toggles --}}
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="auto_apply_enabled" class="toggle toggle-sm" />
                    <span class="text-sm">{{ __('Auto-apply') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="toggle toggle-sm" />
                    <span class="text-sm">{{ __('Active') }}</span>
                </label>
            </div>

            {{-- Cooldown --}}
            <div>
                <label class="text-sm font-medium mb-1 block">{{ __('Cooldown (hours)') }}</label>
                <input type="range" wire:model="cooldown_hours" min="1" max="2160" class="range range-sm w-full" />
                <div class="flex justify-between text-xs text-zinc-500 mt-1">
                    <span>1h</span>
                    <span>{{ $cooldown_hours }}h {{ __('(~ '.round($cooldown_hours / 24).' days)') }}</span>
                    <span>2160h</span>
                </div>
            </div>
        </div>

        <x-slot:actions>
            <x-button variant="ghost" wire:click="$set('showForm', false)">
                {{ __('Cancel') }}
            </x-button>
            <x-button variant="primary" wire:click="save" class="gap-2">
                <x-icon name="tabler.check" class="w-4 h-4" /> {{ __('Save') }}
            </x-button>
        </x-slot:actions>
    </x-modal>
</div>
