@props([
    'name' => null,
])

@if($name)
    @php
        $componentName = str_replace('.', '-', $name);
        if (!str_contains($componentName, '-')) {
            $componentName = 'tabler-' . $componentName;
        }
    @endphp

    <x-dynamic-component
        :component="$componentName"
        {{ $attributes }}
    />
@endif
