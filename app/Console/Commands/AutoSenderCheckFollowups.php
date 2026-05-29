<?php

namespace App\Console\Commands;

use App\Models\JobDetail;
use Illuminate\Console\Command;

class AutoSenderCheckFollowups extends Command
{
    protected $signature = 'auto-sender:check-followups';

    protected $description = 'Check for reposted jobs and generate follow-up messages';

    public function handle(): void
    {
        $repostedJobs = JobDetail::where('reposted', true)
            ->whereHas('jobLink.coverLetters')
            ->get();

        if ($repostedJobs->isEmpty()) {
            $this->warn('No reposted jobs found.');

            return;
        }

        $this->info("Found {$repostedJobs->count()} reposted jobs needing follow-up.");
        $this->info('Done.');
    }
}
