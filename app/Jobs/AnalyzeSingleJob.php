<?php

namespace App\Jobs;

use App\Models\JobDetail;
use App\Models\JobLink;
use App\Services\RabotaMdScraper;
use App\Services\SimilarityDetector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnalyzeSingleJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobLink $jobLink,
    ) {}

    public function handle(RabotaMdScraper $scraper, SimilarityDetector $detector): void
    {
        try {
            $dto = $scraper->fetchJobDetails($this->jobLink->job_url);

            if ($dto === null) {
                $this->jobLink->update(['status' => 'failed']);

                return;
            }

            // Create or update job details
            $detail = $this->jobLink->detail()->updateOrCreate(
                ['job_link_id' => $this->jobLink->id],
                [
                    'full_description' => $dto->fullDescription,
                    'technologies' => $dto->technologies,
                    'salary_from' => $dto->salaryFrom,
                    'salary_to' => $dto->salaryTo,
                    'salary_currency' => $dto->salaryCurrency,
                    'company_name' => $dto->companyName ?? $this->jobLink->company_name,
                    'contact_email' => $dto->contactEmail,
                    'recruiter_name' => $dto->recruiterName,
                    'phone' => $dto->phone,
                    'requirements' => $dto->requirements,
                    'responsibilities' => $dto->responsibilities,
                    'seniority' => $dto->seniority,
                    'work_type' => $dto->workType,
                    'publication_date' => $dto->publicationDate ? now()->parse($dto->publicationDate) : null,
                ]
            );

            // Run similarity detection for repost marking
            $result = $detector->detect($this->jobLink, $detail);

            if ($result['isReposted'] && $result['previousId'] !== null) {
                $previous = JobDetail::find($result['previousId']);

                $repostedAfterDays = $previous?->created_at
                    ? $previous->created_at->diffInDays($this->jobLink->first_seen_at)
                    : null;

                $detail->update([
                    'reposted' => true,
                    'repost_count' => ($previous?->repost_count ?? 0) + 1,
                    'reposted_after_days' => $repostedAfterDays,
                    'similarity_hash' => $result['hash'],
                    'similarity_score' => $result['score'],
                ]);
            }

            // Update job link status
            $this->jobLink->update(['status' => 'processed']);
        } catch (\Exception $e) {
            Log::error('Failed to analyze job', [
                'job_link_id' => $this->jobLink->id,
                'url' => $this->jobLink->job_url,
                'error' => $e->getMessage(),
            ]);

            $this->jobLink->update(['status' => 'failed']);
        }
    }
}
