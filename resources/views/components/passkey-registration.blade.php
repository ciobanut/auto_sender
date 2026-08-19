@assets
@vite('resources/js/passkeys.js')
@endassets

<div x-data="{
        supported: false,
        showForm: false,
        name: '',
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        init() {
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async register() {
            if (!this.name.trim()) return;

            this.loading = true;
            this.error = null;

            try {
                await window.Passkeys.register({ name: this.name });
                this.name = '';
                this.showForm = false;
                await $wire.loadPasskeys();
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
        cancel() {
            this.showForm = false;
            this.name = '';
            this.error = null;
        },
    }">
    <template x-if="!supported">
        <p class="text-sm text-muted-foreground">{{ __('Passkeys are not supported in this browser.') }}</p>
    </template>

    <template x-if="supported && !showForm">
        <div>
            <x-ui.button x-on:click="showForm = true">
                <x-ui.icon name="tabler.plus" class="h-4 w-4" /> {{ __('Add passkey') }}
            </x-ui.button>
        </div>
    </template>

    <template x-if="supported && showForm">
        <div class="space-y-4 rounded-lg border border-border bg-muted/50 p-4" x-init="$nextTick(() => $el.querySelector('input')?.focus())">
            <x-ui.input :label="__('Passkey name')" x-model="name" placeholder="{{ __('e.g., MacBook Pro, iPhone') }}" x-on:keydown.enter.prevent="register()" />
            <p class="text-sm text-muted-foreground">{{ __('Give this passkey a name to help you identify it later.') }}</p>

            <p x-show="error" x-text="error" x-cloak class="text-sm text-destructive"></p>

            <div class="flex gap-2">
                <x-ui.button x-on:click="register()" x-bind:disabled="loading || !name.trim()">
                    <span x-show="!loading">{{ __('Register passkey') }}</span>
                    <span x-show="loading" x-cloak>{{ __('Registering...') }}</span>
                </x-ui.button>
                <x-ui.button variant="ghost" x-on:click="cancel()">
                    {{ __('Cancel') }}
                </x-ui.button>
            </div>
        </div>
    </template>
</div>
