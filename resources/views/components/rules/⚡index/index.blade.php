<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Sending Rules') }}</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Configure cooldowns, limits, and safe mode for automatic applications.') }}</p>
    </div>

    @if($this->rules->isEmpty())
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-12 text-center">
        <x-icon name="tabler.settings" class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
        <h3 class="text-lg font-medium mb-2">{{ __('No custom rules') }}</h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Default cooldown of 30 days applies. Add rules to override per keyword or company.') }}</p>
    </div>
    @else
    <div class="bg-base-100 rounded-xl border border-base-content/5 overflow-hidden">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Target') }}</th>
                    <th>{{ __('Cooldown') }}</th>
                    <th>{{ __('Max/Period') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->rules as $rule)
                <tr>
                    <td>{{ $rule->keyword_id ? __('Keyword') : __('Company') }}</td>
                    <td>{{ $rule->keyword?->keyword ?? $rule->company_domain }}</td>
                    <td>{{ $rule->cooldown_hours }}h</td>
                    <td>{{ $rule->max_applications }}/{{ $rule->period_hours }}h</td>
                    <td>
                        <x-button class="btn-ghost btn-xs">{{ __('Edit') }}</x-button>
                        <x-button class="btn-ghost btn-xs text-error">{{ __('Delete') }}</x-button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
