<?php

use App\Jobs\AnalyzeSingleJob;
use App\Models\JobLink;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public bool $isAnalyzing = false;

    public int $successCount = 0;

    public int $failCount = 0;

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
        $this->successCount = 0;
        $this->failCount = 0;

        foreach ($this->pendingJobs as $job) {
            try {
                AnalyzeSingleJob::dispatch($job);
                $this->successCount++;
            } catch (Exception $e) {
                $this->failCount++;
            }
        }

        $this->isAnalyzing = false;

        if ($this->failCount > 0) {
            $this->dispatch('toast', message: __('Analyzed :success jobs, :fail failed.', ['success' => $this->successCount, 'fail' => $this->failCount]), type: 'warning');
        } else {
            $this->dispatch('toast', message: __('Successfully analyzed :count jobs.', ['count' => $this->successCount]), type: 'success');
        }

        $this->dispatch('analysis-completed');
    }
};
