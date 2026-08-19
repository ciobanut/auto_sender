@props([
    'activateByRoute' => false,
    'activeBgColor' => 'bg-accent',
    'title' => null,
    'icon' => null,
    'separator' => false,
])

<ul {{ $attributes->class(["flex flex-col gap-1 p-1"]) }}>
    @if($title)
        <li class="px-2 py-1.5 text-xs font-medium text-muted-foreground">{{ $title }}</li>
    @endif

    @if($separator)
        <div class="my-1 h-px bg-border"></div>
    @endif

    {{ $slot }}
</ul>
