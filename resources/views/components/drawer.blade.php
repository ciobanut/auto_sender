@props([
    'id',
    'right' => false,
])

<div
    x-data="{ open: false }"
    @open-drawer-{{ $id }}.window="open = true"
    @close-drawer-{{ $id }}.window="open = false"
    {{ $attributes->class(["fixed inset-0 z-50 hidden", "open:flex" => false]) }}
>
    <!-- Overlay -->
    <div
        x-show="open"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/80"
        @click="open = false"
    ></div>

    <!-- Panel -->
    <div
        x-show="open"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @click.away="open = false"
        class="fixed inset-y-0 {{ $right ? 'right-0' : 'left-0' }} z-50 h-full w-3/4 max-w-sm bg-background p-6 shadow-lg sm:w-72"
    >
        {{ $slot }}
    </div>
</div>
