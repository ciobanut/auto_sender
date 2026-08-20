<?php

namespace App\Console\Commands;

use App\Jobs\FetchKeywordJobs;
use App\Models\JobKeyword;
use App\Models\PipelineSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class AutoSenderFetchJobs extends Command
{
    protected $signature = 'auto-sender:fetch-jobs';

    protected $description = 'Fetch new job listings for all active keywords';

    public function handle(): void
    {
        $keywords = JobKeyword::whereIsActive(true)->get();

        if ($keywords->isEmpty()) {
            $this->warn('No active keywords found.');

            return;
        }

        $limit = PipelineSetting::where('user_id', Auth::id())->value('fetch_concurrent') ?? 3;
        $keywordsToProcess = $keywords->take($limit);

        $this->info("Dispatching fetch jobs for {$keywordsToProcess->count()} keywords (limit: {$limit})...");

        foreach ($keywordsToProcess as $keyword) {
            FetchKeywordJobs::dispatch($keyword);
            $this->line("  Dispatched: {$keyword->keyword}");
        }

        $this->info('Done.');
    }
}
