<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ __('AI Settings') }}</h1>
        <p class="text-sm text-muted-foreground">{{ __('Configure how the AI generates cover letters and analyzes jobs.') }}</p>
    </div>

    <x-ui.card>
        <div class="space-y-6">
            <div>
                <label class="text-sm font-medium mb-1 block">{{ __('AI Model') }}</label>
                <p class="text-sm text-muted-foreground">{{ $this->setting->model }}</p>
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block">{{ __('Temperature') }}</label>
                <p class="text-sm text-muted-foreground">{{ $this->setting->temperature }}</p>
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block">{{ __('Max Tokens') }}</label>
                <p class="text-sm text-muted-foreground">{{ $this->setting->max_tokens }}</p>
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block">{{ __('Language') }}</label>
                <p class="text-sm text-muted-foreground">{{ $this->setting->language }}</p>
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block">{{ __('Tone') }}</label>
                <p class="text-sm text-muted-foreground">{{ $this->setting->tone }}</p>
            </div>

            @if($this->setting->default_instructions)
            <div>
                <label class="text-sm font-medium mb-1 block">{{ __('Default Instructions') }}</label>
                <p class="text-sm text-muted-foreground">{{ Str::limit($this->setting->default_instructions, 200) }}</p>
            </div>
            @endif
        </div>
    </x-ui.card>

    <p class="text-sm text-muted-foreground">{{ __('Full AI configuration will be available in a future update.') }}</p>
</div>
