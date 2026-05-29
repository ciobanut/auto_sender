<?php

namespace App\Console\Commands;

use App\Jobs\AnalyzeSingleJob;
use App\Models\JobLink;
use Illuminate\Console\Command;

class AutoSenderAnalyzeNew extends Command
{
    protected $signature = 'auto-sender:analyze-new';

    protected $description = 'Analyze new and re-fetched job listings';

    public function handle(): void
    {
        $jobs = JobLink::whereIn('status', ['new', 're_fetched'])
            ->whereDoesntHave('detail')
            ->get();

        if ($jobs->isEmpty()) {
            $this->warn('No unanalyzed jobs found.');

            return;
        }

        $this->info("Dispatching analysis for {$jobs->count()} jobs...");

        foreach ($jobs as $job) {
            AnalyzeSingleJob::dispatch($job);
        }

        $this->info('Done.');
    }
}
