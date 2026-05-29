<?php

namespace App\Services;

use App\Models\JobDetail;
use App\Models\JobLink;

class SimilarityDetector
{
    public function detect(JobLink $jobLink, JobDetail $detail): array
    {
        $normalizedTitle = $this->normalize($jobLink->title);
        $companyName = $detail->company_name ?? $jobLink->company_name;

        if (empty($companyName)) {
            return ['isReposted' => false, 'score' => 0.0, 'previousId' => null];
        }

        // Compute similarity hash: normalized title + company name
        $descriptionTokens = $detail->full_description
            ? $this->tokenize($detail->full_description)
            : [];

        $hash = md5($normalizedTitle.'|'.$companyName);

        // Find previous records for the same company
        $previous = JobDetail::where('company_name', $companyName)
            ->where('similarity_hash', $hash)
            ->whereHas('jobLink', fn ($q) => $q->where('id', '!=', $jobLink->id))
            ->latest()
            ->first();

        if ($previous) {
            $score = $this->computeJaccardSimilarity(
                $descriptionTokens,
                $this->tokenize($previous->full_description ?? '')
            );

            return [
                'isReposted' => $score > 0.6,
                'score' => $score,
                'previousId' => $previous->id,
                'hash' => $hash,
            ];
        }

        // No exact hash match — try fuzzy matching on title + company
        $potentialReposts = JobDetail::where('company_name', $companyName)
            ->whereHas('jobLink', fn ($q) => $q->where('id', '!=', $jobLink->id))
            ->get();

        foreach ($potentialReposts as $potential) {
            $score = $this->computeJaccardSimilarity(
                $descriptionTokens,
                $this->tokenize($potential->full_description ?? '')
            );

            if ($score > 0.7) {
                return [
                    'isReposted' => true,
                    'score' => $score,
                    'previousId' => $potential->id,
                    'hash' => $hash,
                ];
            }
        }

        return ['isReposted' => false, 'score' => 0.0, 'previousId' => null, 'hash' => $hash];
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function tokenize(string $text): array
    {
        $text = mb_strtolower($text);
        $text = strip_tags($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);

        $words = array_filter(explode(' ', $text), fn ($w) => strlen($w) > 2);

        return array_count_values($words);
    }

    private function computeJaccardSimilarity(array $tokensA, array $tokensB): float
    {
        $setA = array_keys($tokensA);
        $setB = array_keys($tokensB);

        $intersection = count(array_intersect($setA, $setB));
        $union = count(array_unique(array_merge($setA, $setB)));

        if ($union === 0) {
            return 0.0;
        }

        return $intersection / $union;
    }
}
