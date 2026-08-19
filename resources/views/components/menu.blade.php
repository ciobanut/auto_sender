@props([
    'activateByRoute' => false,
    'activeBgColor' => 'bg-base-300',
    'title' => null,
    'icon' => null,
    'separator' => false,
])

<ul {{ $attributes->class(["menu w-full"]) }}>
    @if($title)
        <li class="menu-title text-inherit uppercase">
            <div class="flex items-center gap-2">
                @if($icon)
                    <x-ui.icon :name="$icon" class="inline-flex w-4 h-4" />
                @endif
                {{ $title }}
            </div>
        </li>
    @endif

    @if($separator)
        <hr class="mb-3 border-t border-base-content/10" />
    @endif

    {{ $slot }}
</ul>
