<?php

namespace App\Livewire\Analytics;

use App\Models\Application;
use App\Models\JobKeyword;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Analytics')]
class Index extends Component
{
    #[Computed]
    public function stats(): array
    {
        $userId = Auth::id();

        $totalApplications = Application::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId($userId))->count();
        $replied = Application::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId($userId))
            ->where('response_received', true)->count();
        $interviews = Application::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId($userId))
            ->where('response_type', 'interview')->count();

        return [
            'total_keywords' => JobKeyword::whereUserId($userId)->count(),
            'total_applications' => $totalApplications,
            'reply_rate' => $totalApplications > 0 ? round(($replied / $totalApplications) * 100) : 0,
            'interview_rate' => $totalApplications > 0 ? round(($interviews / $totalApplications) * 100) : 0,
        ];
    }

    #[Computed]
    public function topKeywords(): array
    {
        return JobKeyword::whereUserId(Auth::id())
            ->withCount('applications')
            ->orderByDesc('applications_count')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.analytics.index')
            ->layout('layouts.app', ['title' => 'Analytics']);
    }
}
