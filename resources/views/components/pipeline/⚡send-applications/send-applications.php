<?php

use App\Models\Application;
use App\Models\CoverLetter;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public bool $isSending = false;

    #[Computed]
    public function approvedLetters()
    {
        return CoverLetter::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->where('status', 'approved')
            ->with(['jobLink', 'keyword'])
            ->latest()
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function sentApplications()
    {
        return Application::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->with(['jobLink', 'coverLetter', 'keyword'])
            ->latest('sent_at')
            ->limit(50)
            ->get();
    }

    public function send(): void
    {
        $this->isSending = true;

        foreach ($this->approvedLetters as $letter) {
            SendApplication::dispatch($letter);
        }

        $this->dispatch('sending-started');
    }
};
