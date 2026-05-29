<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('auto-sender:fetch-jobs')->everySixHours();
Schedule::command('auto-sender:analyze-new')->everySixHours();
Schedule::command('auto-sender:generate-messages')->twiceDaily();
Schedule::command('auto-sender:send-approved')->hourly();
Schedule::command('auto-sender:check-followups')->daily();
Schedule::command('auto-sender:cleanup')->daily();
