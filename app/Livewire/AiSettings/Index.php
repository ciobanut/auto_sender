<?php

namespace App\Livewire\AiSettings;

use App\Models\AiSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Title('AI Settings')]
class Index extends Component
{
    use Toast;

    public function render()
    {
        $setting = AiSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            []
        );

        return view('livewire.ai-settings.index', [
            'setting' => $setting,
        ])->layout('layouts.app', ['title' => 'AI Settings']);
    }
}
