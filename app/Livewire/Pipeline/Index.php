<?php

namespace App\Livewire\Pipeline;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pipeline')]
class Index extends Component
{
    public ?string $stage = null;

    public function mount(?string $stage = null): void
    {
        $this->stage = $stage ?? 'fetch';
    }

    public function render()
    {
        return view('livewire.pipeline.index')
            ->layout('layouts.app', ['title' => 'Pipeline']);
    }
}
