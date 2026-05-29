@props([
    'sidebar' => false,
])

@if($sidebar)
    <a href="{{ route('dashboard') }}" wire:navigate {{ $attributes }}>
        <x-app-logo-icon class="size-8 fill-current text-white dark:text-black" />
        <span class="font-bold">{{ config('app.name', 'Laravel') }}</span>
    </a>
@else
    <a href="{{ route('dashboard') }}" wire:navigate {{ $attributes }}>
        <x-app-logo-icon class="size-8 fill-current text-white dark:text-black" />
        <span class="font-bold">{{ config('app.name', 'Laravel') }}</span>
    </a>
@endif
