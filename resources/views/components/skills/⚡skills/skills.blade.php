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
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-5">
        <form wire:submit="add" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-3">
                <x-input wire:model="name" :label="__('Skill Name')" placeholder="e.g. Docker" />
                <x-select wire:model="category" :label="__('Category')" :options="[['id' => 'backend', 'name' => __('Backend')], ['id' => 'frontend', 'name' => __('Frontend')], ['id' => 'devops', 'name' => __('DevOps')], ['id' => 'other', 'name' => __('Other')]]" />
                <x-select wire:model="proficiency" :label="__('Proficiency')" :options="[['id' => 'beginner', 'name' => __('Beginner')], ['id' => 'intermediate', 'name' => __('Intermediate')], ['id' => 'advanced', 'name' => __('Advanced')]]" />
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
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-12 text-center">
        <x-icon name="tabler.tools" class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
        <h3 class="text-lg font-medium mb-2">{{ __('No extra skills added') }}</h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Add technologies like Docker, Kubernetes, Redis that the AI can mention in follow-up applications.') }}</p>
    </div>
    @else
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($this->skills as $skill)
        <div class="flex items-center justify-between bg-base-100 rounded-xl border border-base-content/5 p-3 group">
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
