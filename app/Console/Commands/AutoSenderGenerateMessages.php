<?php

namespace App\Console\Commands;

use App\Jobs\GenerateCoverLetter;
use App\Models\JobLink;
use Illuminate\Console\Command;

class AutoSenderGenerateMessages extends Command
{
    protected $signature = 'auto-sender:generate-messages';

    protected $description = 'Generate AI cover letters for analyzed jobs';

    public function handle(): void
    {
        $jobs = JobLink::whereHas('detail')
            ->whereDoesntHave('coverLetters')
            ->get();

        if ($jobs->isEmpty()) {
            $this->warn('No jobs awaiting messages found.');

            return;
        }

        $this->info("Dispatching message generation for {$jobs->count()} jobs...");

        foreach ($jobs as $job) {
            GenerateCoverLetter::dispatch($job);
        }

        $this->info('Done.');
    }
}
