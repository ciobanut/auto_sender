<?php

use App\Jobs\FetchKeywordJobs;
use App\Models\JobKeyword;
use App\Models\JobLink;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public bool $isFetching = false;

    #[Computed]
    public function jobLinks()
    {
        return JobLink::whereHas('keyword', fn ($q) => $q->whereUserId(Auth::id()))
            ->with('keyword')
            ->latest('first_seen_at')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function keywords()
    {
        return JobKeyword::whereUserId(Auth::id())->whereIsActive(true)->get();
    }

    public function fetch(): void
    {
        $this->isFetching = true;

        foreach ($this->keywords as $keyword) {
            FetchKeywordJobs::dispatch($keyword);
        }
    }

    public function pollFetch(): void
    {
        unset($this->jobLinks);

        if ($this->jobLinks->isNotEmpty()) {
            $this->isFetching = false;
        }
    }
};
