@props([
    'right' => false,
])

<div
    x-data="{ open: false }"
    {{ $attributes->class(["relative inline-block text-left"]) }}
>
    <!-- Trigger -->
    <div @click="open = !open" @keydown.escape.window="open = false">
        {{ $trigger ?? '' }}
    </div>

    <!-- Dropdown content -->
    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute {{ $right ? 'right-0' : 'left-0' }} z-50 mt-2 min-w-[8rem] overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-md"
        style="display: none;"
    >
        {{ $slot }}
    </div>
</div>
