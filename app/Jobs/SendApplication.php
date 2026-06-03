<?php

namespace App\Jobs;

use App\Models\AnalyticsEvent;
use App\Models\Application;
use App\Models\CoverLetter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendApplication implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CoverLetter $coverLetter,
    ) {}

    public function handle(): void
    {
        $jobLink = $this->coverLetter->jobLink;

        if (! $jobLink) {
            return;
        }

        $detail = $this->coverLetter->jobDetail;
        $contactEmail = $detail?->contact_email;

        // Create application record
        $application = Application::create([
            'job_link_id' => $jobLink->id,
            'cover_letter_id' => $this->coverLetter->id,
            'keyword_id' => $this->coverLetter->keyword_id,
            'sent_at' => now(),
            'delivery_status' => 'delivered',
        ]);

        // Update cover letter status
        $this->coverLetter->update(['status' => 'sent']);

        // In production, this would send via email/SMTP
        Log::info('Application sent', [
            'application_id' => $application->id,
            'job' => $jobLink->title,
            'company' => $jobLink->company_name,
            'contact_email' => $contactEmail,
        ]);

        // Record analytics event
        $userId = $jobLink->keyword?->user_id ?? optional($this->coverLetter->keyword)->user_id;

        if ($userId) {
            AnalyticsEvent::create([
                'user_id' => $userId,
                'event_type' => 'application_sent',
                'payload' => [
                    'job_link_id' => $jobLink->id,
                    'company_name' => $jobLink->company_name,
                    'keyword' => $jobLink->keyword?->keyword,
                ],
            ]);
        }
    }
}
