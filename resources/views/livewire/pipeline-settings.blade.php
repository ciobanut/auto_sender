<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Pipeline Settings') }}</h1>
        <p class="text-sm text-muted-foreground">{{ __('Configure the maximum number of concurrent jobs for each pipeline stage.') }}</p>
    </div>

    <x-ui.card>
        <form wire:submit="save" class="space-y-6 p-6">
            <div>
                <label for="fetch_concurrent" class="text-sm font-medium mb-1 block">{{ __('Fetch Concurrent Jobs') }}</label>
                <x-ui.input 
                    wire:model="fetch_concurrent" 
                    id="fetch_concurrent"
                    type="number" 
                    min="1" 
                    max="10" 
                    required 
                />
            </div>

            <div>
                <label for="analyze_concurrent" class="text-sm font-medium mb-1 block">{{ __('Analysis Concurrent Jobs') }}</label>
                <x-ui.input 
                    wire:model="analyze_concurrent" 
                    id="analyze_concurrent"
                    type="number" 
                    min="1" 
                    max="10" 
                    required 
                />
            </div>

            <div>
                <label for="generate_concurrent" class="text-sm font-medium mb-1 block">{{ __('Generate Concurrent Jobs') }}</label>
                <x-ui.input 
                    wire:model="generate_concurrent" 
                    id="generate_concurrent"
                    type="number" 
                    min="1" 
                    max="10" 
                    required 
                />
            </div>

            <div>
                <label for="send_concurrent" class="text-sm font-medium mb-1 block">{{ __('Send Concurrent Jobs') }}</label>
                <x-ui.input 
                    wire:model="send_concurrent" 
                    id="send_concurrent"
                    type="number" 
                    min="1" 
                    max="10" 
                    required 
                />
            </div>

            <div class="flex items-center gap-4">
                <x-ui.button type="submit">{{ __('Save') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
