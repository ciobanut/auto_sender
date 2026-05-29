<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h3 class="text-lg font-medium">{{ __('Delete account') }}</h3>
        <p class="text-sm opacity-70">{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <x-button variant="error" wire:click="$set('confirmUserDeletion', true)">
        {{ __('Delete account') }}
    </x-button>

    <x-modal wire:model="confirmUserDeletion" title="{{ __('Are you sure you want to delete your account?') }}" subtitle="{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}" class="w-full max-w-lg">
        <form method="POST" wire:submit="deleteUser" class="space-y-6">
            <x-input wire:model="password" :label="__('Password')" type="password" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <x-button label="{{ __('Cancel') }}" wire:click="$set('confirmUserDeletion', false)" />
                <x-button label="{{ __('Delete account') }}" type="submit" class="btn-error" />
            </div>
        </form>
    </x-modal>
</section>
