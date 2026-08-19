@props([
    'id',
    'right' => false,
    'title' => null,
    'subtitle' => null,
    'separator' => false,
    'withCloseButton' => false,
])

<div {{ $attributes->class(["drawer absolute z-50", "drawer-end" => $right]) }}>
    <input
        id="{{ $id }}"
        type="checkbox"
        class="drawer-toggle"
    />

    <div class="drawer-side">
        <label for="{{ $id }}" class="drawer-overlay"></label>

        <div class="menu bg-base-100 text-base-content min-h-full w-80 p-4">
            @if($title)
                <div class="mb-4">
                    <h2 class="text-lg font-bold">{{ $title }}</h2>
                    @if($subtitle)
                        <p class="text-sm text-base-content/70">{{ $subtitle }}</p>
                    @endif
                    @if($separator)
                        <div class="divider"></div>
                    @endif
                </div>
            @endif

            {{ $slot }}

            @if($withCloseButton)
                <label for="{{ $id }}" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
            @endif
        </div>
    </div>
</div>
