<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('Pipeline') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Stage') }}: {{ $stage }}</p>
        </div>
    </div>

    <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
        @php
        $icons = [
            'fetch' => 'tabler.download',
            'analyze' => 'tabler.search',
            'generate' => 'tabler.messages',
            'review' => 'tabler.eye',
            'send' => 'tabler.send',
        ];
        @endphp
        <x-icon name="{{ $icons[$stage] ?? 'tabler.clock' }}" class="w-12 h-12 mx-auto text-primary mb-4" />
        <h3 class="text-lg font-medium mb-2">{{ __('Pipeline stage coming soon') }}</h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('The ":stage" pipeline stage will be fully implemented in the next development phase.', ['stage' => $stage]) }}
        </p>
    </div>
</div>
