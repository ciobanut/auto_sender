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
            document.documentElement.setAttribute('data-theme', this.theme);
        }
    }"
    x-init="document.documentElement.setAttribute('data-theme', this.theme)"
    {{ $attributes }}
>
    <button
        @click="toggle()"
        class="btn btn-ghost btn-sm"
        type="button"
    >
        <template x-if="theme === '{{ $lightTheme }}'">
            <x-ui.icon name="tabler.sun" class="w-5 h-5" />
        </template>
        <template x-if="theme === '{{ $darkTheme }}'">
            <x-ui.icon name="tabler.moon" class="w-5 h-5" />
        </template>
        @if($withLabel)
            <span x-text="theme === '{{ $lightTheme }}' ? '{{ $light }}' : '{{ $dark }}'"></span>
        @endif
    </button>
</div>
