<?php

namespace App\Livewire\Applications;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Title('Application Log')]
class Index extends Component
{
    use Toast;

    public string $filter = '';

    public function render()
    {
        $query = Application::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->with(['jobLink', 'coverLetter', 'keyword'])
            ->latest();

        if ($this->filter) {
            $query->where('delivery_status', $this->filter);
        }

        return view('livewire.applications.index', [
            'applications' => $query->get(),
        ])->layout('layouts.app', ['title' => 'Application Log']);
    }
}
