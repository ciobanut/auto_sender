@props([
    'title' => null,
    'icon' => null,
    'link' => null,
    'href' => null,
    'route' => null,
    'routeParams' => [],
    'external' => false,
    'active' => false,
    'disabled' => false,
    'hidden' => false,
    'exact' => false,
])

@aware(['activateByRoute' => false, 'activeBgColor' => 'bg-accent'])

@if(!$hidden)
    @php
        $url = $link ?? $href ?? ($route ? route($route, $routeParams) : null);
        $isActive = $active || ($activateByRoute && $url && request()->is(parse_url($url, PHP_URL_PATH)));
    @endphp

    <li>
        <a
            {{ $attributes->class([
                "flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors hover:bg-accent hover:text-accent-foreground",
                "bg-accent text-accent-foreground font-medium" => $isActive,
                "pointer-events-none opacity-50" => $disabled,
            ]) }}
            @if($url)
                href="{{ $url }}"
                @if($external) target="_blank" rel="noopener noreferrer" @endif
            @endif
        >
            @if($icon)
                <x-ui.icon :name="$icon" class="h-4 w-4 shrink-0" />
            @endif
            <span>{{ $title ?? $slot }}</span>
        </a>
    </li>
@endif
