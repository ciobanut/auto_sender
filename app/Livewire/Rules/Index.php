<?php

namespace App\Livewire\Rules;

use App\Models\CooldownRule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Title('Sending Rules')]
class Index extends Component
{
    use Toast;

    public function render()
    {
        return view('livewire.rules.index', [
            'rules' => CooldownRule::whereUserId(Auth::id())->get(),
        ])->layout('layouts.app', ['title' => 'Sending Rules']);
    }
}
