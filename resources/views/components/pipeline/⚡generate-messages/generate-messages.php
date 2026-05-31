<?php

use App\Jobs\GenerateCoverLetter;
use App\Models\CoverLetter;
use App\Models\JobLink;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public bool $isGenerating = false;

    #[Computed]
    public function pendingJobs()
    {
        return JobLink::whereHas('keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->whereHas('detail')
            ->whereDoesntHave('coverLetters')
            ->with(['keyword', 'detail'])
            ->latest('first_seen_at')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function generatedDrafts()
    {
        return CoverLetter::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->with(['jobLink', 'jobDetail', 'keyword'])
            ->latest()
            ->limit(50)
            ->get();
    }

    public function generate(): void
    {
        $this->isGenerating = true;

        foreach ($this->pendingJobs as $job) {
            GenerateCoverLetter::dispatch($job);
        }

        $this->dispatch('generation-started');
    }
};
