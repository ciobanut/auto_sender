<?php

use App\Jobs\AnalyzeSingleJob;
use App\Models\JobLink;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public bool $isAnalyzing = false;

    public bool $showJobModal = false;

    public int $selectedJobIndex = 0;

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
    }

    public function pollAnalyze(): void
    {
        unset($this->pendingJobs, $this->analyzedJobs);

        if ($this->pendingJobs->isEmpty()) {
            $this->isAnalyzing = false;
        }
    }

    public function showJob(int $index): void
    {
        $this->selectedJobIndex = $index;
        $this->showJobModal = true;
    }

    public function nextJob(): void
    {
        if ($this->selectedJobIndex < $this->analyzedJobs->count() - 1) {
            $this->selectedJobIndex++;
        }
    }

    public function prevJob(): void
    {
        if ($this->selectedJobIndex > 0) {
            $this->selectedJobIndex--;
        }
    }
};
