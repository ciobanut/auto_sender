<?php

use App\Models\AiSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Title('AI Settings')] #[Layout('layouts.app')] class extends Component
{
    use Toast;

    #[Computed]
    public function setting()
    {
        return AiSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            []
        );
    }
};
