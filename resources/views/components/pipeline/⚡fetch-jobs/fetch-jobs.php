<?php

use App\Jobs\FetchKeywordJobs;
use App\Models\JobKeyword;
use App\Models\JobLink;
use App\Models\PipelineSetting;
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

        $limit = PipelineSetting::where('user_id', Auth::id())->value('fetch_concurrent') ?? 3;

        $this->keywords->take($limit)->each(function ($keyword) {
            FetchKeywordJobs::dispatchSync($keyword);
        });

        $this->isFetching = false;
        unset($this->jobLinks);
    }
};
