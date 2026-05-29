@props([
    'name' => null,
])

<x-dropdown>
    <x-slot:trigger>
        <x-button class="btn-ghost btn-sm" data-test="sidebar-menu-button"><x-tabler.user /></x-button>
    </x-slot:trigger>

    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
        <x-avatar class="!w-8 !h-8" />
        <div class="grid flex-1 text-start text-sm leading-tight">
            <p class="truncate font-medium">{{ auth()->user()->name }}</p>
            <p class="truncate text-xs opacity-50">{{ auth()->user()->email }}</p>
        </div>
    </div>

    <hr class="my-1" />

    <x-menu-item :href="route('profile.edit')" wire:navigate>
        <x-tabler.settings class="size-4 inline shrink-0" /> {{ __('Settings') }}
    </x-menu-item>

    <hr class="my-1" />

    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <x-menu-item link="{{ route('logout') }}" class="w-full cursor-pointer">
            <x-tabler.logout class="size-4 inline shrink-0" /> {{ __('Log out') }}
        </x-menu-item>
    </form>
</x-dropdown>
