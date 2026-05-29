<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <x-menu activate-by-route>
            <x-menu-item :href="route('profile.edit')" wire:navigate>
                <x-icon name="tabler.user" class="w-4 h-4" /> {{ __('Profile') }}
            </x-menu-item>
            <x-menu-item :href="route('security.edit')" wire:navigate>
                <x-icon name="tabler.lock" class="w-4 h-4" /> {{ __('Security') }}
            </x-menu-item>
            <x-menu-item :href="route('appearance.edit')" wire:navigate>
                <x-icon name="tabler.sun" class="w-4 h-4" /> {{ __('Appearance') }}
            </x-menu-item>
        </x-menu>
    </div>

    <hr class="md:hidden w-full my-4" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="text-xl font-bold">{{ $heading ?? '' }}</h2>
        <p class="text-sm opacity-70">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
