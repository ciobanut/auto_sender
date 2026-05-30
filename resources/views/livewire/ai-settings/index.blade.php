<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ __('AI Settings') }}</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Configure how the AI generates cover letters and analyzes jobs.') }}</p>
    </div>

    <div class="bg-base-100 rounded-xl border border-base-content/5 p-6 space-y-6">
        <div>
            <label class="text-sm font-medium mb-1 block">{{ __('AI Model') }}</label>
            <p class="text-xs text-zinc-500 mb-2">{{ $setting->model }}</p>
        </div>

        <div>
            <label class="text-sm font-medium mb-1 block">{{ __('Temperature') }}</label>
            <p class="text-xs text-zinc-500">{{ $setting->temperature }}</p>
        </div>

        <div>
            <label class="text-sm font-medium mb-1 block">{{ __('Max Tokens') }}</label>
            <p class="text-xs text-zinc-500">{{ $setting->max_tokens }}</p>
        </div>

        <div>
            <label class="text-sm font-medium mb-1 block">{{ __('Language') }}</label>
            <p class="text-xs text-zinc-500">{{ $setting->language }}</p>
        </div>

        <div>
            <label class="text-sm font-medium mb-1 block">{{ __('Tone') }}</label>
            <p class="text-xs text-zinc-500">{{ $setting->tone }}</p>
        </div>

        @if($setting->default_instructions)
        <div>
            <label class="text-sm font-medium mb-1 block">{{ __('Default Instructions') }}</label>
            <p class="text-xs text-zinc-500">{{ Str::limit($setting->default_instructions, 200) }}</p>
        </div>
        @endif
    </div>

    <p class="text-sm text-zinc-500">{{ __('Full AI configuration will be available in a future update.') }}</p>
</div>
