<?php

namespace App\Jobs;

use App\Models\JobKeyword;
use App\Models\JobLink;
use App\Services\RabotaMdScraper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class FetchKeywordJobs implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobKeyword $keyword,
    ) {
        $this->onQueue('scraping');
    }

    public function handle(RabotaMdScraper $scraper): void
    {
        $dtoJobs = $scraper->fetchJobs($this->keyword->keyword);

        if ($dtoJobs->isEmpty()) {
            return;
        }

        $now = now();

        DB::transaction(function () use ($dtoJobs, $now) {
            foreach ($dtoJobs as $dto) {
                // Use upsert for duplicate detection by job_url
                $existing = JobLink::where('job_url', $dto->jobUrl)->first();

                if ($existing) {
                    $existing->increment('fetch_count');
                    $existing->update([
                        're_fetched_at' => $now,
                        'status' => 're_fetched',
                    ]);
                } else {
                    JobLink::create([
                        'keyword_id' => $this->keyword->id,
                        'job_url' => $dto->jobUrl,
                        'external_job_id' => $dto->externalJobId,
                        'title' => $dto->title,
                        'company_name' => $dto->companyName,
                        'location' => $dto->location,
                        'short_preview' => $dto->shortPreview,
                        'status' => 'new',
                        'fetch_count' => 1,
                        'first_seen_at' => $now,
                    ]);
                }
            }
        });
    }
}
