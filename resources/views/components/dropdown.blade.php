@props([
    'right' => false,
])

<div {{ $attributes->class(["dropdown", "dropdown-end" => $right]) }}>
    <div tabindex="0" role="button" class="cursor-pointer">
        {{ $trigger ?? $slot }}
    </div>
    <ul
        tabindex="0"
        class="menu dropdown-content bg-base-100 rounded-box z-[1] w-52 p-2 shadow-lg"
    >
        {{ $slot }}
    </ul>
</div>
