<?php

namespace App\Livewire;

use App\Models\PipelineSetting;
use App\Traits\Toast;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PipelineSettings extends Component
{
    use Toast;

    public int $fetch_concurrent = 3;

    public int $analyze_concurrent = 3;

    public int $generate_concurrent = 3;

    public int $send_concurrent = 3;

    public function mount(): void
    {
        $setting = PipelineSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            []
        );

        $this->fetch_concurrent = $setting->fetch_concurrent;
        $this->analyze_concurrent = $setting->analyze_concurrent;
        $this->generate_concurrent = $setting->generate_concurrent;
        $this->send_concurrent = $setting->send_concurrent;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'fetch_concurrent' => 'required|integer|min:1|max:10',
            'analyze_concurrent' => 'required|integer|min:1|max:10',
            'generate_concurrent' => 'required|integer|min:1|max:10',
            'send_concurrent' => 'required|integer|min:1|max:10',
        ]);

        PipelineSetting::where('user_id', Auth::id())->update($validated);

        $this->success(__('Pipeline settings updated successfully.'));
    }
}
