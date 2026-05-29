<section class="w-full">
    @include('partials.settings-heading')

    <h2 class="sr-only">{{ __('Appearance settings') }}</h2>

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <div x-data="{
            theme: localStorage.getItem('theme') || 'light',
            init() {
                this.applyTheme();
            },
            setTheme(t) {
                this.theme = t;
                localStorage.setItem('theme', t);
                this.applyTheme();
            },
            applyTheme() {
                let html = document.documentElement;
                if (this.theme === 'dark') {
                    html.classList.add('dark');
                } else if (this.theme === 'light') {
                    html.classList.remove('dark');
                } else {
                    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        html.classList.add('dark');
                    } else {
                        html.classList.remove('dark');
                    }
                }
            }
        }">
            <div class="flex gap-4">
                <button type="button" class="btn flex-1" x-bind:class="theme === 'light' ? 'btn-primary' : 'btn-ghost'" x-on:click="setTheme('light')">
                    <x-tabler.sun class="size-5" />
                    <span>{{ __('Light') }}</span>
                </button>
                <button type="button" class="btn flex-1" x-bind:class="theme === 'dark' ? 'btn-primary' : 'btn-ghost'" x-on:click="setTheme('dark')">
                    <x-tabler.moon class="size-5" />
                    <span>{{ __('Dark') }}</span>
                </button>
                <button type="button" class="btn flex-1" x-bind:class="theme === 'system' ? 'btn-primary' : 'btn-ghost'" x-on:click="setTheme('system')">
                    <x-tabler.device-desktop class="size-5" />
                    <span>{{ __('System') }}</span>
                </button>
            </div>
        </div>
    </x-settings.layout>
</section>
