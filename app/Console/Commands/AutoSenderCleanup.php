<?php

namespace App\Console\Commands;

use App\Models\AnalyticsEvent;
use Illuminate\Console\Command;

class AutoSenderCleanup extends Command
{
    protected $signature = 'auto-sender:cleanup';

    protected $description = 'Cleanup old logs and cache';

    public function handle(): void
    {
        // Remove analytics events older than 90 days
        $deleted = AnalyticsEvent::where('created_at', '<', now()->subDays(90))->delete();

        $this->info("Cleaned up {$deleted} old analytics events.");
    }
}
