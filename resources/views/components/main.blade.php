@props([
    'fullWidth' => false,
])

<main @class(["w-full mx-auto", "max-w-screen-2xl" => !$fullWidth])>
    <div class="relative flex min-h-screen">
        @if(isset($sidebar) && $sidebar)
            <!-- Sidebar for mobile -->
            <div class="lg:hidden">
                {{ $sidebar }}
            </div>

            <!-- Sidebar for desktop -->
            <div class="hidden lg:block lg:w-64 lg:shrink-0 lg:border-r">
                <div class="sticky top-0 h-screen overflow-y-auto">
                    {{ $sidebar }}
                </div>
            </div>
        @endif

        <!-- Main content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </div>
    </div>
</main>
