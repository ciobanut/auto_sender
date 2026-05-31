<?php

namespace App\Livewire\Dashboard;

use App\Models\Application;
use App\Models\CoverLetter;
use App\Models\JobLink;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Index extends Component
{
    public int $step = 1;

    public string $activeStage = 'fetch';

    public function setStage(string $stage): void
    {
        $this->activeStage = $stage;

        $map = ['fetch' => 1, 'analyze' => 2, 'generate' => 3, 'review' => 4, 'send' => 5];
        $this->step = $map[$stage] ?? 1;
    }

    #[Computed]
    public function activeStage(): string
    {
        return match ($this->step) {
            2 => 'analyze',
            3 => 'generate',
            4 => 'review',
            5 => 'send',
            default => 'fetch',
        };
    }

    #[Computed]
    public function stageCounts(): array
    {
        $userId = Auth::id();

        return [
            'fetch' => JobLink::whereHas('keyword', fn ($q) => $q->whereUserId($userId))
                ->whereIn('status', ['new', 're_fetched'])
                ->count(),

            'analyze' => JobLink::whereHas('keyword', fn ($q) => $q->whereUserId($userId))
                ->whereHas('detail')
                ->count(),

            'generate' => CoverLetter::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId($userId))
                ->where('status', 'draft')
                ->count(),

            'review' => CoverLetter::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId($userId))
                ->whereIn('status', ['draft', 'edited'])
                ->count(),

            'send' => Application::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId($userId))
                ->where('delivery_status', 'pending')
                ->count(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.index')
            ->layout('layouts.app', ['title' => 'Dashboarsdfsd']);
    }
}
