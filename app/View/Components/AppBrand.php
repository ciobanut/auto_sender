<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppBrand extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct() {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
                        <div class="flex items-center gap-2 w-fit">
                            <x-app-logo-icon class="w-6 h-6" />
                            <span class="font-bold text-3xl me-3">
                                {{ config('app.name', 'Laravel') }}
                            </span>
                        </div>
                    </div>

                    <div class="display-when-collapsed hidden mx-5 mt-5 mb-1">
                        <x-app-logo-icon class="size-7" />
                    </div>
                </a>
            HTML;
    }
}
