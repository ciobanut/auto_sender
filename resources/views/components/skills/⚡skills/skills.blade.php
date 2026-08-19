<div class="space-y-6 max-w-3xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('Extra Skills') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('Technologies not in your CV that the AI can inject into follow-up messages.') }}</p>
        </div>
        <x-ui.button wire:click="$set('showForm', true)">
            <x-ui.icon name="tabler.plus" class="h-4 w-4" /> {{ __('Add Skill') }}
        </x-ui.button>
    </div>

    {{-- Inline add form --}}
    @if($showForm)
    <x-ui.card>
        <form wire:submit="add" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-3">
                <x-ui.input wire:model="name" :label="__('Skill Name')" placeholder="e.g. Docker" />
                <x-ui.select wire:model="category" :label="__('Category')" :options="[['id' => 'backend', 'name' => __('Backend')], ['id' => 'frontend', 'name' => __('Frontend')], ['id' => 'devops', 'name' => __('DevOps')], ['id' => 'other', 'name' => __('Other')]]" />
                <x-ui.select wire:model="proficiency" :label="__('Proficiency')" :options="[['id' => 'beginner', 'name' => __('Beginner')], ['id' => 'intermediate', 'name' => __('Intermediate')], ['id' => 'advanced', 'name' => __('Advanced')]]" />
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button type="submit">
                    <x-ui.icon name="tabler.check" class="h-4 w-4" /> {{ __('Add') }}
                </x-ui.button>
                <x-ui.button variant="ghost" wire:click="$set('showForm', false)">
                    {{ __('Cancel') }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
    @endif

    @if($this->skills->isEmpty())
    <x-ui.card>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <x-ui.icon name="tabler.tools" class="text-muted-foreground mb-3 h-12 w-12" />
            <h3 class="text-lg font-medium mb-2">{{ __('No extra skills added') }}</h3>
            <p class="text-muted-foreground text-sm">{{ __('Add technologies like Docker, Kubernetes, Redis that the AI can mention in follow-up applications.') }}</p>
        </div>
    </x-ui.card>
    @else
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($this->skills as $skill)
        <x-ui.card class="flex items-center justify-between p-3 group">
            <div class="flex items-center gap-3 min-w-0">
                <x-ui.icon name="tabler.code" class="h-4 w-4 text-primary shrink-0" />
                <div class="min-w-0">
                    <span class="font-medium">{{ $skill->name }}</span>
                    <span class="text-xs text-muted-foreground ml-2">{{ $skill->category }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <x-ui.badge variant="soft" class="hidden sm:inline-flex text-xs">{{ $skill->proficiency }}</x-ui.badge>
                <x-ui.button variant="ghost" size="icon-xs" class="text-destructive opacity-0 group-hover:opacity-100 transition-opacity" wire:click="remove({{ $skill->id }})" wire:confirm="{{ __('Remove this skill?') }}">
                    <x-ui.icon name="tabler.x" class="h-3.5 w-3.5" />
                </x-ui.button>
            </div>
        </x-ui.card>
        @endforeach
    </div>
    @endif
</div>
