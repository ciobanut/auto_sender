@props([
    'id' => null,
    'title' => null,
    'subtitle' => null,
    'separator' => false,
    'persistent' => false,
    'boxClass' => null,
])

<div
    x-data="{ open: false }"
    @if($id)
        @open-dialog-{{ $id }}.window="open = true"
        @close-dialog-{{ $id }}.window="open = false"
    @endif
    {{ $attributes }}
>
    @if($slot)
        <div x-show="open" style="display: none;">
            <!-- Overlay -->
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 bg-black/80"
                @if(!$persistent) @click="open = false" @endif
            ></div>

            <!-- Dialog -->
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="w-full max-w-lg rounded-lg border bg-background p-6 shadow-lg {{ $boxClass }}"
                >
                    @if(!$persistent)
                        <button
                            type="button"
                            @click="open = false"
                            class="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                            <span class="sr-only">Close</span>
                        </button>
                    @endif

                    @if($title)
                        <div class="mb-4">
                            <h2 class="text-lg font-semibold leading-none tracking-tight">{{ $title }}</h2>
                            @if($subtitle)
                                <p class="text-sm text-muted-foreground">{{ $subtitle }}</p>
                            @endif
                        </div>
                    @endif

                    {{ $slot }}

                    @if(isset($actions) && $actions)
                        <div class="mt-4 flex justify-end gap-2">
                            {{ $actions }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
