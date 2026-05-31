<?php

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Title('Application Log')] #[Layout('layouts.app')] class extends Component
{
    use Toast;

    public string $filter = '';

    #[Computed]
    public function applications()
    {
        $query = Application::whereHas('jobLink.keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->with(['jobLink', 'coverLetter', 'keyword'])
            ->latest();

        if ($this->filter) {
            $query->where('delivery_status', $this->filter);
        }

        return $query->get();
    }
};
