@props([
    'label' => null,
    'icon' => null,
    'errorField' => null,
    'errorClass' => 'text-error',
    'omitError' => false,
    'firstErrorOnly' => false,
])

@php
    $errorBag = $errorField ? $errors->get($errorField) : $errors->all();
    $errorMessage = $firstErrorOnly ? reset($errorBag) : implode(' ', $errorBag);
@endphp

<div class="fieldset" {{ $attributes->only(['wire:model', 'wire:model.live']) }}>
    @if($label)
        <label class="fieldset-legend">{{ $label }}</label>
    @endif

    <div class="relative">
        @if($icon)
            <x-ui.icon :name="$icon" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-base-content/50" />
        @endif

        <input
            type="password"
            {{ $attributes->class([
                "input input-bordered w-full",
                "pl-10" => $icon
            ])->except(['wire:model', 'wire:model.live', 'class']) }}
        />
    </div>

    @if(!$omitError && $errorMessage)
        <p class="{{ $errorClass }}">{{ $errorMessage }}</p>
    @endif
</div>
