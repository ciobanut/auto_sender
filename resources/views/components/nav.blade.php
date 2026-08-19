@props([
    'sticky' => false,
    'fullWidth' => false,
])

<div {{ $attributes->class(["border-b bg-background", "sticky top-0 z-10" => $sticky]) }}>
    <div @class(["flex items-center px-4 py-3 sm:px-6", "mx-auto max-w-screen-2xl" => !$fullWidth])>
        <div {{ isset($brand) ? $brand->attributes->class(["flex-1 flex items-center"]) : '' }}>
            {{ $brand ?? '' }}
        </div>
        <div {{ isset($actions) ? $actions->attributes->class(["flex items-center gap-4"]) : '' }}>
            {{ $actions ?? '' }}
        </div>
    </div>
</div>
