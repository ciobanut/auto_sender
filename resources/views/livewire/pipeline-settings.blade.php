<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Pipeline Settings') }}</h1>
        <p class="text-sm text-muted-foreground">{{ __('Configure the maximum number of concurrent jobs for each pipeline stage.') }}</p>
    </div>

    <x-ui.card>
        <form wire:submit="save" class="space-y-6 p-6">
            <x-ui.input 
                wire:model="fetch_concurrent" 
                :label="__('Fetch Concurrent Jobs')" 
                type="number" 
                min="1" 
                max="10" 
                required 
            />

            <x-ui.input 
                wire:model="analyze_concurrent" 
                :label="__('Analysis Concurrent Jobs')" 
                type="number" 
                min="1" 
                max="10" 
                required 
            />

            <x-ui.input 
                wire:model="generate_concurrent" 
                :label="__('Generate Concurrent Jobs')" 
                type="number" 
                min="1" 
                max="10" 
                required 
            />

            <x-ui.input 
                wire:model="send_concurrent" 
                :label="__('Send Concurrent Jobs')" 
                type="number" 
                min="1" 
                max="10" 
                required 
            />

            <div class="flex items-center gap-4">
                <x-ui.button type="submit">{{ __('Save') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
