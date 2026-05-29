<div
    class="py-6 space-y-6 border shadow-sm rounded-xl border-zinc-200 dark:border-white/10"
    wire:cloak
    x-data="{ showRecoveryCodes: false }"
>
    <div class="px-6 space-y-2">
        <div class="flex items-center gap-2">
            <x-icon name="tabler.lock" class="w-4 h-4" />
            <h3 class="text-lg font-medium">{{ __('2FA recovery codes') }}</h3>
        </div>
        <p class="text-sm opacity-70">
            {{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.') }}
        </p>
    </div>

    <div class="px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <x-button
                x-show="!showRecoveryCodes"
                variant="primary"
                x-on:click="showRecoveryCodes = true"
            >
                <x-icon name="tabler.eye" class="w-4 h-4" /> {{ __('View recovery codes') }}
            </x-button>

            <x-button
                x-show="showRecoveryCodes"
                variant="primary"
                x-on:click="showRecoveryCodes = false"
                x-cloak
            >
                <x-icon name="tabler.eye-off" class="w-4 h-4" /> {{ __('Hide recovery codes') }}
            </x-button>

            @if (filled($recoveryCodes))
                <x-button
                    x-show="showRecoveryCodes"
                    variant="outline"
                    wire:click="regenerateRecoveryCodes"
                    x-cloak
                >
                    <x-icon name="tabler.refresh" class="w-4 h-4" /> {{ __('Regenerate codes') }}
                </x-button>
            @endif
        </div>

        <div
            x-show="showRecoveryCodes"
            x-transition
            id="recovery-codes-section"
            class="relative overflow-hidden"
            x-cloak
        >
            <div class="mt-3 space-y-3">
                @error('recoveryCodes')
                    <x-alert title="{{ $message }}" />
                @enderror

                @if (filled($recoveryCodes))
                    <div
                        class="grid gap-1 p-4 font-mono text-sm rounded-lg bg-zinc-100 dark:bg-white/5"
                        role="list"
                        aria-label="{{ __('Recovery codes') }}"
                    >
                        @foreach($recoveryCodes as $code)
                            <div
                                role="listitem"
                                class="select-text"
                                wire:loading.class="opacity-50 animate-pulse"
                            >
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs opacity-70">
                        {{ __('Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate codes above.') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
