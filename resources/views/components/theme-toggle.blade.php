@props([
    'light' => 'Light',
    'dark' => 'Dark',
    'lightTheme' => 'light',
    'darkTheme' => 'dark',
    'withLabel' => false,
])

<div
    x-data="{
        theme: $persist(window.matchMedia('(prefers-color-scheme: dark)').matches ? '{{ $darkTheme }}' : '{{ $lightTheme }}').as('theme'),
        toggle() {
            this.theme = this.theme === '{{ $lightTheme }}' ? '{{ $darkTheme }}' : '{{ $lightTheme }}';
            document.documentElement.classList.toggle('dark', this.theme === '{{ $darkTheme }}');
            if (window.Flux) {
                window.Flux.applyAppearance(this.theme);
            }
        }
    }"
    x-init="
        document.documentElement.classList.toggle('dark', this.theme === '{{ $darkTheme }}');
        if (window.Flux) {
            window.Flux.applyAppearance(this.theme);
        }
    " 
    {{ $attributes }}
>
    <button
        @click="toggle()"
        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-9 px-3"
        type="button"
    >
        <template x-if="theme === '{{ $lightTheme }}'">
            <x-ui.icon name="tabler.sun" class="h-5 w-5" />
        </template>
        <template x-if="theme === '{{ $darkTheme }}'">
            <x-ui.icon name="tabler.moon" class="h-5 w-5" />
        </template>
        @if($withLabel)
            <span class="ml-2" x-text="theme === '{{ $lightTheme }}' ? '{{ $light }}' : '{{ $dark }}'"></span>
        @endif
    </button>
</div>
