<div class="space-y-6 max-w-3xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('Extra Skills') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Technologies not in your CV that the AI can inject into follow-up messages.') }}</p>
        </div>
        <x-button variant="primary" wire:click="$set('showForm', true)">
            <x-icon name="tabler.plus" class="w-4 h-4" /> {{ __('Add Skill') }}
        </x-button>
    </div>

    {{-- Inline add form --}}
    @if($showForm)
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <form wire:submit="add" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-3">
                    <x-input wire:model="name" :label="__('Skill Name')" placeholder="e.g. Docker" />
                    <div>
                        <label class="text-sm font-medium mb-1 block">{{ __('Category') }}</label>
                        <select wire:model="category" class="select select-bordered w-full">
                            <option value="backend">{{ __('Backend') }}</option>
                            <option value="frontend">{{ __('Frontend') }}</option>
                            <option value="devops">{{ __('DevOps') }}</option>
                            <option value="other">{{ __('Other') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1 block">{{ __('Proficiency') }}</label>
                        <select wire:model="proficiency" class="select select-bordered w-full">
                            <option value="beginner">{{ __('Beginner') }}</option>
                            <option value="intermediate">{{ __('Intermediate') }}</option>
                            <option value="advanced">{{ __('Advanced') }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-button variant="primary" type="submit">
                        <x-icon name="tabler.check" class="w-4 h-4" /> {{ __('Add') }}
                    </x-button>
                    <x-button variant="ghost" wire:click="$set('showForm', false)">
                        {{ __('Cancel') }}
                    </x-button>
                </div>
            </form>
        </div>
    @endif

    @if($this->skills->isEmpty())
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
            <x-icon name="tabler.tools" class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
            <h3 class="text-lg font-medium mb-2">{{ __('No extra skills added') }}</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Add technologies like Docker, Kubernetes, Redis that the AI can mention in follow-up applications.') }}</p>
        </div>
    @else
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($this->skills as $skill)
                <div class="flex items-center justify-between bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-3 group">
                    <div class="flex items-center gap-3 min-w-0">
                        <x-icon name="tabler.code" class="w-4 h-4 text-primary shrink-0" />
                        <div class="min-w-0">
                            <span class="font-medium">{{ $skill->name }}</span>
                            <span class="text-xs text-zinc-500 ml-2">{{ $skill->category }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="badge badge-sm badge-ghost hidden sm:inline">{{ $skill->proficiency }}</span>
                        <button wire:click="remove({{ $skill->id }})" wire:confirm="{{ __('Remove this skill?') }}" class="btn-ghost btn-xs text-error opacity-0 group-hover:opacity-100 transition-opacity">
                            <x-icon name="tabler.x" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
