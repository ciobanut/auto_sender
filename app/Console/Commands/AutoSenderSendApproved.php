<?php

namespace App\Console\Commands;

use App\Jobs\SendApplication;
use App\Models\CoverLetter;
use Illuminate\Console\Command;

class AutoSenderSendApproved extends Command
{
    protected $signature = 'auto-sender:send-approved';

    protected $description = 'Send approved applications';

    public function handle(): void
    {
        $letters = CoverLetter::where('status', 'approved')->get();

        if ($letters->isEmpty()) {
            $this->warn('No approved applications found.');

            return;
        }

        $this->info("Sending {$letters->count()} approved applications...");

        foreach ($letters as $letter) {
            SendApplication::dispatch($letter);
        }

        $this->info('Done.');
    }
}
