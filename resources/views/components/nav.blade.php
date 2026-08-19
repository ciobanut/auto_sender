@props([
    'sticky' => false,
    'fullWidth' => false,
])

<div {{ $attributes->class(["bg-base-100 border-base-content/10 border-b", "sticky top-0 z-10" => $sticky]) }}>
    <div @class(["flex items-center px-6 py-3", "max-w-screen-2xl mx-auto" => !$fullWidth])>
        <div {{ isset($brand) ? $brand->attributes->class(["flex-1 flex items-center"]) : '' }}>
            {{ $brand ?? '' }}
        </div>
        <div {{ isset($actions) ? $actions->attributes->class(["flex items-center gap-4"]) : '' }}>
            {{ $actions ?? '' }}
        </div>
    </div>
</div>
