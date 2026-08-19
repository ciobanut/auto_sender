@props([
    'id' => null,
    'title' => null,
    'subtitle' => null,
    'separator' => false,
    'persistent' => false,
    'boxClass' => null,
])

<dialog
    {{ $attributes->class(["modal"])->except(['wire:model', 'wire:model.live']) }}
    @if($id)
        id="{{ $id }}"
    @else
        x-data="{ open: @entangle($attributes->wire('model'))->live }"
        x-init="$watch('open', value => { if (!value){ $dispatch('close') }else{ $dispatch('open') } })"
        :class="{'modal-open !animate-none': open}"
        :open="open"
        @if(!$persistent)
            @keydown.escape.window = "$wire.{{ $attributes->wire('model')->value() }} = false"
        @endif
    @endif
>
    <div class="modal-box {{ $boxClass }}">
        @if(!$persistent)
            <form method="dialog" tabindex="-1">
                @if($id)
                    <button class="btn btn-circle btn-sm btn-ghost absolute end-2 top-2 z-[999]" type="submit" tabindex="-1">✕</button>
                @else
                    <button class="btn btn-circle btn-sm btn-ghost absolute end-2 top-2 z-[999]" @click="$wire.{{ $attributes->wire('model')->value() }} = false" tabindex="-1">✕</button>
                @endif
            </form>
        @endif

        @if($title)
            <div class="mb-4">
                <h2 class="text-xl font-bold">{{ $title }}</h2>
                @if($subtitle)
                    <p class="text-sm text-base-content/70">{{ $subtitle }}</p>
                @endif
                @if($separator)
                    <div class="divider"></div>
                @endif
            </div>
        @endif

        <div>
            {{ $slot }}
        </div>

        @if(isset($actions) && $actions)
            <div class="modal-action">
                {{ $actions }}
            </div>
        @endif
    </div>

    @if(!$persistent)
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    @endif
</dialog>
