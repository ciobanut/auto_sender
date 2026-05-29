<?php

namespace App\Livewire\Pipeline;

use App\Models\CoverLetter;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Mary\Traits\Toast;

class ReviewApplications extends Component
{
    use Toast;

    public ?int $selectedLetterId = null;

    public string $editedContent = '';

    #[Computed]
    public function pendingLetters()
    {
        return CoverLetter::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->whereIn('status', ['draft', 'edited'])
            ->with(['jobLink', 'jobDetail', 'keyword'])
            ->latest()
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function selectedLetter()
    {
        if (! $this->selectedLetterId) {
            return null;
        }

        return CoverLetter::with(['jobLink', 'jobDetail', 'keyword'])
            ->find($this->selectedLetterId);
    }

    public function select(int $id): void
    {
        $this->selectedLetterId = $id;
        $letter = CoverLetter::find($id);
        $this->editedContent = $letter?->editable_content ?? $letter?->content ?? '';
    }

    public function saveEdit(): void
    {
        $letter = CoverLetter::findOrFail($this->selectedLetterId);
        $letter->update([
            'editable_content' => $this->editedContent,
            'status' => 'edited',
        ]);

        $this->success(__('Changes saved.'));
    }

    public function approve(int $id): void
    {
        CoverLetter::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->findOrFail($id)
            ->update(['status' => 'approved']);

        if ($this->selectedLetterId === $id) {
            $this->reset(['selectedLetterId', 'editedContent']);
        }
    }

    public function reject(int $id): void
    {
        CoverLetter::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->findOrFail($id)
            ->update(['status' => 'draft']);

        if ($this->selectedLetterId === $id) {
            $this->reset(['selectedLetterId', 'editedContent']);
        }
    }

    public function render()
    {
        return view('livewire.pipeline.review-applications');
    }
}
