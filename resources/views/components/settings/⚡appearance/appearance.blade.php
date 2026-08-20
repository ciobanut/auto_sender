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
                if (window.Flux) {
                    window.Flux.applyAppearance(this.theme);
                }
            }
        }">
            <div class="flex gap-4">
                <x-ui.button variant="default" class="flex-1" x-bind:class="theme === 'light' ? '' : 'opacity-50'" @click="setTheme('light')">
                    <x-ui.icon name="tabler.sun" class="h-5 w-5" />
                    <span>{{ __('Light') }}</span>
                </x-ui.button>
                <x-ui.button variant="default" class="flex-1" x-bind:class="theme === 'dark' ? '' : 'opacity-50'" @click="setTheme('dark')">
                    <x-ui.icon name="tabler.moon" class="h-5 w-5" />
                    <span>{{ __('Dark') }}</span>
                </x-ui.button>
                <x-ui.button variant="default" class="flex-1" x-bind:class="theme === 'system' ? '' : 'opacity-50'" @click="setTheme('system')">
                    <x-ui.icon name="tabler.device-desktop" class="h-5 w-5" />
                    <span>{{ __('System') }}</span>
                </x-ui.button>
            </div>
        </div>
    </x-settings.layout>
</section>
