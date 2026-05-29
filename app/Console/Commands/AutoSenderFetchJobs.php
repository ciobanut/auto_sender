<?php

namespace App\Console\Commands;

use App\Jobs\FetchKeywordJobs;
use App\Models\JobKeyword;
use Illuminate\Console\Command;

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

        $this->info("Dispatching fetch jobs for {$keywords->count()} keywords...");

        foreach ($keywords as $keyword) {
            FetchKeywordJobs::dispatch($keyword);
            $this->line("  Dispatched: {$keyword->keyword}");
        }

        $this->info('Done.');
    }
}
