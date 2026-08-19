@props([
    'label' => null,
    'icon' => null,
    'errorField' => null,
    'errorClass' => 'text-destructive',
    'omitError' => false,
    'firstErrorOnly' => false,
])

@php
    $errorBag = $errorField ? $errors->get($errorField) : $errors->all();
    $errorMessage = $firstErrorOnly ? reset($errorBag) : implode(' ', $errorBag);
@endphp

<div class="space-y-2" {{ $attributes->only(['wire:model', 'wire:model.live']) }}>
    @if($label)
        <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">{{ $label }}</label>
    @endif

    <div class="relative">
        @if($icon)
            <x-ui.icon :name="$icon" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        @endif

        <input
            type="password"
            {{ $attributes->class([
                "flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50",
                "pl-9" => $icon
            ])->except(['wire:model', 'wire:model.live', 'class']) }}
        />
    </div>

    @if(!$omitError && $errorMessage)
        <p class="{{ $errorClass }} text-sm">{{ $errorMessage }}</p>
    @endif
</div>
