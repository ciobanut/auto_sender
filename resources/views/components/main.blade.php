@props([
    'fullWidth' => false,
])

<main @class(["w-full mx-auto", "max-w-screen-2xl" => !$fullWidth])>
    <div class="drawer lg:drawer-open">
        @if(isset($sidebar) && $sidebar)
            <input id="{{ $sidebar->attributes['drawer'] ?? 'main-drawer' }}" type="checkbox" class="drawer-toggle" />
        @endif

        <div class="drawer-content w-full mx-auto p-5 lg:px-10 lg:py-5">
            {{ $slot }}
        </div>

        @if(isset($sidebar) && $sidebar)
            <div class="drawer-side">
                <label for="{{ $sidebar->attributes['drawer'] ?? 'main-drawer' }}" class="drawer-overlay"></label>
                <div class="bg-base-100 min-h-full w-64 lg:w-72">
                    {{ $sidebar }}
                </div>
            </div>
        @endif
    </div>
</main>
