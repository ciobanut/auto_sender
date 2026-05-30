<?php

namespace App\Livewire\Pipeline;

use App\Jobs\AnalyzeSingleJob;
use App\Models\JobLink;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AnalyzeJobs extends Component
{
    public bool $isAnalyzing = false;

    #[Computed]
    public function pendingJobs()
    {
        return JobLink::whereHas('keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->whereIn('status', ['new', 're_fetched'])
            ->whereDoesntHave('detail')
            ->with('keyword')
            ->latest('first_seen_at')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function analyzedJobs()
    {
        return JobLink::whereHas('keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->whereHas('detail')
            ->with(['keyword', 'detail'])
            ->latest('first_seen_at')
            ->limit(50)
            ->get();
    }

    public function analyze(): void
    {
        $this->isAnalyzing = true;

        foreach ($this->pendingJobs as $job) {
            AnalyzeSingleJob::dispatch($job);
        }

        $this->dispatch('analysis-started');
    }

    public function render()
    {
        return view('livewire.pipeline.analyze-jobs');
    }
}
