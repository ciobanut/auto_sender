<section class="w-full">
    @include('partials.settings-heading')

    <h2 class="sr-only">{{ __('Security settings') }}</h2>

    <x-settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <x-password wire:model="current_password" :label="__('Current password')" required autocomplete="current-password" />
            <x-password wire:model="password" :label="__('New password')" required autocomplete="new-password" />
            <x-password wire:model="password_confirmation" :label="__('Confirm password')" required autocomplete="new-password" />

            <div class="flex items-center gap-4">
                <x-ui.button type="submit">{{ __('Save') }}</x-ui.button>
            </div>
        </form>

        @if ($canManageTwoFactor)
        <section class="mt-12">
            <h3 class="text-lg font-medium">{{ __('Two-factor authentication') }}</h3>
            <p class="text-sm text-muted-foreground">{{ __('Manage your two-factor authentication settings') }}</p>

            <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
                @if ($twoFactorEnabled)
                <div class="space-y-4">
                    <p>
                        {{ __('You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                    </p>

                    <div class="flex justify-start">
                        <x-ui.button variant="destructive" wire:click="disable">
                            {{ __('Disable 2FA') }}
                        </x-ui.button>
                    </div>

                    <livewire:settings.two-factor.recovery-codes :$requiresConfirmation />
                </div>
                @else
                <div class="space-y-4">
                    <p class="text-muted-foreground">
                        {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                    </p>

                    <x-ui.button wire:click="enable">
                        {{ __('Enable 2FA') }}
                    </x-ui.button>
                </div>
                @endif
            </div>
        </section>
        @endif

        @if ($canManageTwoFactor)
        <x-modal wire:model="showModal" title="{{ $this->modalConfig['title'] }}" subtitle="{{ $this->modalConfig['description'] }}" class="w-full max-w-md">
            @if ($showVerificationStep)
            <div class="space-y-6">
                <div class="flex flex-col items-center space-y-3 justify-center" x-data x-init="$nextTick(() => $el.querySelector('input')?.focus())">
                    <x-pin wire:model="code" length="6" />
                    @error('code')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @else
            <div class="space-y-6">
                <div class="rounded-lg border border-border overflow-hidden h-48">
                    @empty($qrCodeSvg)
                    <div class="flex items-center justify-center w-full h-full bg-muted/50 animate-pulse">
                        <x-ui.icon name="tabler.refresh" class="h-5 w-5 animate-spin" />
                    </div>
                    @else
                    <div x-data class="flex items-center justify-center h-full p-4">
                        <div class="bg-white p-3 rounded" x-bind:style="($flux.dark ?? false) ? 'filter: invert(1) brightness(1.5)' : ''">
                            {!! $qrCodeSvg !!}
                        </div>
                    </div>
                    @endempty
                </div>
            </div>

            <div>
                <x-ui.button :disabled="$errors->has('setupData')" class="w-full" wire:click="showVerificationIfNecessary">
                    {{ $this->modalConfig['buttonText'] }}
                </x-ui.button>
            </div>

            <div class="space-y-4">
                <div class="relative flex items-center justify-center w-full">
                    <div class="absolute inset-0 w-full h-px top-1/2 bg-border"></div>
                    <span class="relative px-2 text-sm bg-background text-muted-foreground">
                        {{ __('or, enter the code manually') }}
                    </span>
                </div>

                <div x-data="{
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
                        }">
                    <div class="flex items-stretch w-full border rounded-xl border-border">
                        @empty($manualSetupKey)
                        <div class="flex items-center justify-center w-full p-3 bg-muted">
                            <x-ui.icon name="tabler.refresh" class="h-5 w-5 animate-spin" />
                        </div>
                        @else
                        <input type="text" readonly value="{{ $manualSetupKey }}" class="w-full p-3 bg-transparent outline-none text-foreground" />

                        <button @click="copy()" class="px-3 transition-colors border-l cursor-pointer border-border">
                            <x-ui.icon name="tabler.copy" x-show="!copied" class="h-5 w-5" />
                            <x-ui.icon name="tabler.check" x-show="copied" class="h-5 w-5 text-success" x-cloak />
                        </button>
                        @endempty
                    </div>
                </div>
            </div>
            @endif

            <x-slot:actions>
                <x-ui.button wire:click="closeModal" >{{ __('Close') }}</x-ui.button>
            </x-slot:actions>
        </x-modal>
        @endif

        @if ($canManagePasskeys)
        <section class="mt-12">
            <h3 class="text-lg font-medium">{{ __('Passkeys') }}</h3>
            <p class="text-sm text-muted-foreground">{{ __('Manage your passkeys for passwordless sign-in') }}</p>

            <div class="mt-6 flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
                <div class="rounded-lg border border-border overflow-hidden">
                    @forelse ($passkeys as $passkey)
                    <div class="flex items-center justify-between p-4 {{ ! $loop->last ? 'border-b border-border' : '' }}">
                        <div class="flex items-center gap-4">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-muted">
                                <x-ui.icon name="tabler.key" class="h-5 w-5 text-muted-foreground" />
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2.5">
                                    <p class="font-medium tracking-tight">{{ $passkey['name'] }}</p>
                                    @if ($passkey['authenticator'])
                                    <x-ui.badge variant="soft">{{ $passkey['authenticator'] }}</x-ui.badge>
                                    @endif
                                </div>
                                <p class="text-muted-foreground text-xs">
                                    {{ __('Added :time', ['time' => $passkey['created_at_diff']]) }}
                                    @if ($passkey['last_used_at_diff'])
                                    <span class="opacity-50 mx-1">/</span>
                                    {{ __('Last used :time', ['time' => $passkey['last_used_at_diff']]) }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <x-ui.button variant="ghost" size="icon" class="text-destructive hover:text-destructive hover:bg-destructive/10" wire:click="confirmDelete({{ $passkey['id'] }})">
                            <x-ui.icon name="tabler.trash" class="h-4 w-4" />
                        </x-ui.button>
                    </div>
                    @empty
                    <div class="p-8 text-center">
                        <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-muted">
                            <x-ui.icon name="tabler.key" class="h-7 w-7 text-muted-foreground" />
                        </div>
                        <p class="font-medium">{{ __('No passkeys yet') }}</p>
                        <p class="text-sm text-muted-foreground mt-1">{{ __('Add a passkey to sign in without a password') }}</p>
                    </div>
                    @endforelse
                </div>

                <x-passkey-registration />
            </div>
        </section>
        @endif

        <x-modal wire:model="showDeleteModal" title="{{ __('Remove passkey') }}" subtitle="{{ __('Are you sure you want to remove the passkey :name? You will no longer be able to use it to sign in.', ['name' => $deletingPasskeyName]) }}" class="w-full max-w-md">
            <x-slot:actions>
                <x-ui.button wire:click="closeDeleteModal" >{{ __('Cancel') }}</x-ui.button>
                <x-ui.button wire:click="deletePasskey" variant="destructive" >{{ __('Remove passkey') }}</x-ui.button>
            </x-slot:actions>
        </x-modal>
    </x-settings.layout>
</section>
