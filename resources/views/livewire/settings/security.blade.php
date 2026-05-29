<section class="w-full">
    @include('partials.settings-heading')

    <h2 class="sr-only">{{ __('Security settings') }}</h2>

    <x-settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <x-password
                wire:model="current_password"
                :label="__('Current password')"
                required
                autocomplete="current-password"
            />
            <x-password
                wire:model="password"
                :label="__('New password')"
                required
                autocomplete="new-password"
            />
            <x-password
                wire:model="password_confirmation"
                :label="__('Confirm password')"
                required
                autocomplete="new-password"
            />

            <div class="flex items-center gap-4">
                <x-button variant="primary" type="submit" data-test="update-password-button">{{ __('Save') }}</x-button>
            </div>
        </form>

        @if ($canManageTwoFactor)
            <section class="mt-12">
                <h3 class="text-lg font-medium">{{ __('Two-factor authentication') }}</h3>
                <p class="text-sm opacity-70">{{ __('Manage your two-factor authentication settings') }}</p>

                <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
                    @if ($twoFactorEnabled)
                        <div class="space-y-4">
                            <p>
                                {{ __('You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                            </p>

                            <div class="flex justify-start">
                                <x-button
                                    variant="error"
                                    wire:click="disable"
                                >
                                    {{ __('Disable 2FA') }}
                                </x-button>
                            </div>

                            <livewire:settings.two-factor.recovery-codes :$requiresConfirmation />
                        </div>
                    @else
                        <div class="space-y-4">
                            <p class="opacity-70">
                                {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                            </p>

                            <x-button
                                variant="primary"
                                wire:click="enable"
                            >
                                {{ __('Enable 2FA') }}
                            </x-button>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if ($canManageTwoFactor)
            <x-modal wire:model="showModal" title="{{ $this->modalConfig['title'] }}" subtitle="{{ $this->modalConfig['description'] }}" class="w-full max-w-md">
                @if ($showVerificationStep)
                    <div class="space-y-6">
                        <div
                            class="flex flex-col items-center space-y-3 justify-center"
                            x-data
                            x-init="$nextTick(() => $el.querySelector('input')?.focus())"
                        >
                            <x-pin
                                wire:model="code"
                                length="6"
                                numeric
                                label="OTP Code"
                                label:sr-only
                                class="mx-auto"
                            />
                        </div>

                        <div class="flex items-center space-x-3">
                            <x-button
                                variant="outline"
                                class="flex-1"
                                wire:click="resetVerification"
                            >
                                {{ __('Back') }}
                            </x-button>

                            <x-button
                                variant="primary"
                                class="flex-1"
                                wire:click="confirmTwoFactor"
                                x-bind:disabled="$wire.code.length < 6"
                            >
                                {{ __('Confirm') }}
                            </x-button>
                        </div>
                    </div>
                @else
                    @error('setupData')
                        <x-alert title="{{ $message }}" class="mb-4" />
                    @enderror

                    <div class="flex justify-center">
                        <div class="relative w-64 overflow-hidden border rounded-lg border-stone-200 dark:border-stone-700 aspect-square">
                            @empty($qrCodeSvg)
                                <div class="absolute inset-0 flex items-center justify-center bg-white dark:bg-stone-700 animate-pulse">
                                    <x-tabler.refresh class="size-5 animate-spin" />
                                </div>
                            @else
                            <div x-data class="flex items-center justify-center h-full p-4">
                                <div
                                    class="bg-white p-3 rounded"
                                    x-bind:style="($flux.dark ?? false) ? 'filter: invert(1) brightness(1.5)' : ''"
                                >
                                        {!! $qrCodeSvg !!}
                                    </div>
                                </div>
                            @endempty
                        </div>
                    </div>

                    <div>
                        <x-button
                            :disabled="$errors->has('setupData')"
                            variant="primary"
                            class="w-full"
                            wire:click="showVerificationIfNecessary"
                        >
                            {{ $this->modalConfig['buttonText'] }}
                        </x-button>
                    </div>

                    <div class="space-y-4">
                        <div class="relative flex items-center justify-center w-full">
                            <div class="absolute inset-0 w-full h-px top-1/2 bg-stone-200 dark:bg-stone-600"></div>
                            <span class="relative px-2 text-sm bg-white dark:bg-stone-800 text-stone-600 dark:text-stone-400">
                                {{ __('or, enter the code manually') }}
                            </span>
                        </div>

                        <div
                            class="flex items-center space-x-2"
                            x-data="{
                                copied: false,
                                async copy() {
                                    try {
                                        await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 1500);
                                    } catch (e) {
                                        console.warn('Could not copy to clipboard');
                                    }
                                }
                            }"
                        >
                            <div class="flex items-stretch w-full border rounded-xl dark:border-stone-700">
                                @empty($manualSetupKey)
                                    <div class="flex items-center justify-center w-full p-3 bg-stone-100 dark:bg-stone-700">
                                        <x-tabler.refresh class="size-5 animate-spin" />
                                    </div>
                                @else
                                    <input
                                        type="text"
                                        readonly
                                        value="{{ $manualSetupKey }}"
                                        class="w-full p-3 bg-transparent outline-none text-stone-900 dark:text-stone-100"
                                    />

                                    <button
                                        @click="copy()"
                                        class="px-3 transition-colors border-l cursor-pointer border-stone-200 dark:border-stone-600"
                                    >
                                        <x-tabler.copy x-show="!copied" class="size-5" />
                                        <x-tabler.check x-show="copied" class="size-5 text-green-500" x-cloak />
                                    </button>
                                @endempty
                            </div>
                        </div>
                    </div>
                @endif

                <x-slot:actions>
                    <x-button label="{{ __('Close') }}" wire:click="closeModal" />
                </x-slot:actions>
            </x-modal>
        @endif

        @if ($canManagePasskeys)
            <section class="mt-12">
                <h3 class="text-lg font-medium">{{ __('Passkeys') }}</h3>
                <p class="text-sm opacity-70">{{ __('Manage your passkeys for passwordless sign-in') }}</p>

                <div class="mt-6 flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
                    <div class="border rounded-lg border-zinc-200 dark:border-zinc-700 overflow-hidden">
                        @forelse ($passkeys as $passkey)
                            <div class="flex items-center justify-between p-4 {{ ! $loop->last ? 'border-b border-zinc-200 dark:border-zinc-700' : '' }}">
                                <div class="flex items-center gap-4">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                                        <x-tabler.key class="size-5 text-zinc-500 dark:text-zinc-400" />
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2.5">
                                            <p class="font-medium tracking-tight">{{ $passkey['name'] }}</p>
                                            @if ($passkey['authenticator'])
                                                <x-badge :value="$passkey['authenticator']" class="badge-sm" />
                                            @endif
                                        </div>
                                        <p class="text-zinc-500 dark:text-zinc-400 text-xs">
                                            {{ __('Added :time', ['time' => $passkey['created_at_diff']]) }}
                                            @if ($passkey['last_used_at_diff'])
                                                <span class="opacity-50 mx-1">/</span>
                                                {{ __('Last used :time', ['time' => $passkey['last_used_at_diff']]) }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <x-button
                                    variant="ghost"
                                    class="btn-sm text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50"
                                    wire:click="confirmDelete({{ $passkey['id'] }})"
                                >
                                    <x-tabler.trash class="size-4" />
                                </x-button>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                                    <x-tabler.key class="size-7 text-zinc-400 dark:text-zinc-500" />
                                </div>
                                <p class="font-medium">{{ __('No passkeys yet') }}</p>
                                <p class="text-sm opacity-70 mt-1">{{ __('Add a passkey to sign in without a password') }}</p>
                            </div>
                        @endforelse
                    </div>

                    <x-passkey-registration />
                </div>
            </section>
        @endif

        <x-modal wire:model="showDeleteModal" title="{{ __('Remove passkey') }}" subtitle="{{ __('Are you sure you want to remove the passkey :name? You will no longer be able to use it to sign in.', ['name' => $deletingPasskeyName]) }}" class="w-full max-w-md">
            <x-slot:actions>
                <x-button label="{{ __('Cancel') }}" wire:click="closeDeleteModal" />
                <x-button label="{{ __('Remove passkey') }}" wire:click="deletePasskey" class="btn-error" />
            </x-slot:actions>
        </x-modal>
    </x-settings.layout>
</section>
