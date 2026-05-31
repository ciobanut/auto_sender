<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pipeline')] #[Layout('layouts.app')] class extends Component
{
    public ?string $stage = null;

    public function mount(?string $stage = null): void
    {
        $this->stage = $stage ?? 'fetch';
    }
};
