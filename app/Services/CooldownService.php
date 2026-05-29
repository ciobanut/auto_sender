<?php

namespace App\Services;

use App\Models\Application;
use App\Models\CooldownRule;
use App\Models\JobLink;
use App\Models\User;

class CooldownService
{
    public function canApply(JobLink $jobLink, User $user): array
    {
        $keyword = $jobLink->keyword;
        $companyName = $jobLink->company_name;

        // If reposted, allow regardless of cooldown
        if ($jobLink->detail?->reposted) {
            return ['allowed' => true, 'reason' => 'reposted'];
        }

        // Check per-keyword override
        $keywordRule = CooldownRule::whereUserId($user->id)
            ->where('keyword_id', $keyword?->id)
            ->first();

        if ($keywordRule) {
            return $this->evaluateRule($jobLink, $keywordRule);
        }

        // Check per-company override
        $companyRule = CooldownRule::whereUserId($user->id)
            ->where('company_domain', $this->extractDomain($companyName))
            ->first();

        if ($companyRule) {
            return $this->evaluateRule($jobLink, $companyRule);
        }

        // Use keyword-level cooldown as default
        $cooldownHours = $keyword?->cooldown_hours ?? 720;

        return $this->evaluateDefault($jobLink, $user, $cooldownHours);
    }

    private function evaluateRule(JobLink $jobLink, CooldownRule $rule): array
    {
        $recentCount = Application::where('job_link_id', $jobLink->id)
            ->where('sent_at', '>=', now()->subHours($rule->period_hours))
            ->count();

        if ($recentCount >= $rule->max_applications) {
            return [
                'allowed' => false,
                'reason' => 'Max applications reached for the period.',
            ];
        }

        $lastApplication = Application::where('job_link_id', $jobLink->id)
            ->latest('sent_at')
            ->first();

        if ($lastApplication && $lastApplication->sent_at->addHours($rule->cooldown_hours)->isFuture()) {
            $waitHours = now()->diffInHours($lastApplication->sent_at->addHours($rule->cooldown_hours));

            return [
                'allowed' => false,
                'reason' => "Cooldown active. Wait {$waitHours} more hours.",
            ];
        }

        return ['allowed' => true, 'reason' => null];
    }

    private function evaluateDefault(JobLink $jobLink, User $user, int $cooldownHours): array
    {
        $lastApplication = Application::whereHas('jobLink', function ($q) use ($jobLink, $user) {
            $q->where('company_name', $jobLink->company_name)
                ->whereHas('keyword', fn ($k) => $k->whereUserId($user->id));
        })->latest('sent_at')->first();

        if ($lastApplication && $lastApplication->sent_at->addHours($cooldownHours)->isFuture()) {
            $waitHours = now()->diffInHours($lastApplication->sent_at->addHours($cooldownHours));

            return [
                'allowed' => false,
                'reason' => "Default cooldown active. Wait {$waitHours} more hours.",
            ];
        }

        return ['allowed' => true, 'reason' => null];
    }

    private function extractDomain(string $companyName): string
    {
        // Convert company name to a domain-like key for matching
        return strtolower(preg_replace('/[^a-z0-9]/', '', $companyName));
    }
}
