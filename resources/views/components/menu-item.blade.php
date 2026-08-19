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

@aware(['activateByRoute' => false, 'activeBgColor' => 'bg-base-300'])

@if(!$hidden)
    @php
        $url = $link ?? $href ?? ($route ? route($route, $routeParams) : null);
        $isActive = $active || ($activateByRoute && $url && request()->is(parse_url($url, PHP_URL_PATH)));
    @endphp

    <li @class(['disabled' => $disabled])>
        <a
            {{ $attributes->class([
                "my-0.5 py-1.5 px-4 hover:text-inherit whitespace-nowrap",
                "active $activeBgColor" => $isActive
            ]) }}
            @if($url)
                href="{{ $url }}"
                @if($external) target="_blank" @endif
            @endif
        >
            @if($icon)
                <x-ui.icon :name="$icon" class="w-4 h-4" />
            @endif
            {{ $title ?? $slot }}
        </a>
    </li>
@endif
