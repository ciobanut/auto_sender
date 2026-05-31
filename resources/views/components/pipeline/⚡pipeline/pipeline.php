<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    public ?string $stage = null;

    public function mount(?string $stage = null): void
    {
        $this->stage = $stage ?? 'fetch';
    }
};
