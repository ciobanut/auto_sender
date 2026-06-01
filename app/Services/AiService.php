<?php

namespace App\Services;

use App\Ai\Agents\CoverLetterWriterAgent;
use App\Ai\Agents\FollowUpWriterAgent;
use App\Ai\Agents\TechnologyExtractorAgent;
use App\Models\CoverLetter;
use App\Models\JobDetail;
use Illuminate\Support\Facades\Log;

class AiService
{
    private const INITIAL_SYSTEM_PROMPT = <<<'PROMPT'
You are a professional cover letter writer. Write in {{language}}.
Use a {{tone}} tone. Keep it concise (max {{max_tokens}} tokens).

CONTEXT:
Job Description:
{{job_description}}

Technologies Required:
{{technologies}}

Candidate CV:
{{cv_text}}

Additional Skills (only mention if relevant):
{{extra_skills}}

User Instructions:
{{custom_instructions}}

RULES:
- Do not invent experience the candidate doesn't have
- Be specific about why this candidate fits THIS job
- No generic filler sentences
- Mention 1-2 specific technologies from the job description
- Keep it to 3-4 short paragraphs
- Sign the letter with the candidate's name: {{user_name}}

OUTPUT FORMAT:
{
  "cover_letter": "text",
  "confidence_score": 0.0-1.0,
  "match_reasons": ["reason1", "reason2"],
  "matched_technologies": ["tech1", "tech2"]
}
PROMPT;

    private const FOLLOW_UP_SYSTEM_PROMPT = <<<'PROMPT'
You are writing a FOLLOW-UP cover letter.
The candidate ALREADY applied to this job previously.
Write in {{language}}. Use {{tone}} tone. Keep it concise.

CONTEXT:
Job Description:
{{job_description}}

Technologies Required:
{{technologies}}

Candidate CV:
{{cv_text}}

Previously Sent Message:
{{previous_cover_letter}}

NEW Extra Skills (not in original CV, acquired since):
{{extra_skills}}

User Instructions:
{{custom_instructions}}

RULES:
- Reference the previous application politely
- Acknowledge no response was received (do not sound bitter)
- Express continued interest
- Mention 1-2 NEW skills/experiences not in the original application
- Keep it to 2-3 short paragraphs
- Do not apologize for re-applying
- Sign the letter with the candidate's name: {{user_name}}

OUTPUT FORMAT:
{
  "cover_letter": "text",
  "confidence_score": 0.0-1.0,
  "new_skills_highlighted": ["skill1", "skill2"],
  "match_reasons": ["reason1"]
}
PROMPT;

    private const EXTRACTION_PROMPT = <<<'PROMPT'
Extract all technologies, frameworks, and tools mentioned in this job description. Categorize them.

Job Description:
{{text}}

OUTPUT FORMAT:
{
  "technologies": ["PHP", "Laravel"],
  "seniority": "Middle" | "Senior" | "Lead",
  "work_type": "remote" | "hybrid" | "office",
  "salary_mentioned": true | false,
  "salary_from": number | null,
  "salary_to": number | null
}
PROMPT;

    private const DEFAULT_MODEL = 'deepseek-v4-flash';

    private const DEFAULT_TEMPERATURE = 0.7;

    private const DEFAULT_MAX_TOKENS = 500;

    private const DEFAULT_LANGUAGE = 'English';

    private const DEFAULT_TONE = 'professional';

    public function generateCoverLetter(
        JobDetail $jobDetail,
        string $cvText,
        array $extraSkills,
        string $instructions,
        ?CoverLetter $previousLetter = null,
        array $settings = [],
        string $userName = '',
    ): array {
        $isFollowUp = $previousLetter !== null;

        // Check cache first
        $cacheKey = AiCache::key($jobDetail, $cvText, $instructions, $isFollowUp);
        $cached = AiCache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $model = $settings['model'] ?? self::DEFAULT_MODEL;
        $temperature = $settings['temperature'] ?? self::DEFAULT_TEMPERATURE;
        $maxTokens = $settings['max_tokens'] ?? self::DEFAULT_MAX_TOKENS;
        $language = $settings['language'] ?? self::DEFAULT_LANGUAGE;
        $tone = $settings['tone'] ?? self::DEFAULT_TONE;

        $systemPrompt = $isFollowUp
            ? $this->buildFollowUpPrompt($jobDetail, $cvText, $extraSkills, $instructions, $previousLetter, $language, $tone, $maxTokens, $userName)
            : $this->buildInitialPrompt($jobDetail, $cvText, $extraSkills, $instructions, $language, $tone, $maxTokens, $userName);

        try {
            $agent = $isFollowUp
                ? new FollowUpWriterAgent(
                    dynamicInstructions: $systemPrompt,
                    dynamicTemperature: $temperature,
                    dynamicMaxTokens: $maxTokens,
                )
                : new CoverLetterWriterAgent(
                    dynamicInstructions: $systemPrompt,
                    dynamicTemperature: $temperature,
                    dynamicMaxTokens: $maxTokens,
                );

            $response = $agent->prompt(
                prompt: 'Generate a cover letter based on the provided context.',
                model: $model,
            );

            $raw = method_exists($response, 'toArray') ? $response->toArray() : [];

            $result = $this->normalizeResult($raw, $jobDetail, $extraSkills, $isFollowUp, $userName);

            // Cache the result
            AiCache::set($cacheKey, $result);

            return $result;
        } catch (\Exception $e) {
            Log::error('AI cover letter generation failed', [
                'job_detail_id' => $jobDetail->id,
                'error' => $e->getMessage(),
                'is_follow_up' => $isFollowUp,
            ]);

            return $this->fallbackResult($jobDetail, $extraSkills, $isFollowUp, $userName);
        }
    }

    public function extractTechnologies(string $description): array
    {
        // Use the regex-based extraction from RabotaMdScraper as a fast path
        $scraper = new RabotaMdScraper;
        $technologies = $scraper->extractTechnologies($description);

        // If the scraper found enough, return early (no API call needed)
        if (count($technologies) >= 3) {
            return $technologies;
        }

        // Otherwise, try AI-powered extraction for better coverage
        try {
            $prompt = str_replace('{{text}}', substr($description, 0, 4000), self::EXTRACTION_PROMPT);

            $agent = new TechnologyExtractorAgent(
                dynamicInstructions: $prompt,
                dynamicTemperature: 0.1,
                dynamicMaxTokens: 300,
            );

            $response = $agent->prompt(
                prompt: 'Extract technologies from this job description.',
                model: self::DEFAULT_MODEL,
            );

            $data = method_exists($response, 'toArray') ? $response->toArray() : [];
            $aiTechnologies = $data['technologies'] ?? [];

            if (! empty($aiTechnologies)) {
                return array_unique(array_merge($technologies, $aiTechnologies));
            }
        } catch (\Exception $e) {
            Log::debug('AI technology extraction failed, using regex-only results', [
                'error' => $e->getMessage(),
            ]);
        }

        return $technologies;
    }

    private function buildInitialPrompt(
        JobDetail $jobDetail,
        string $cvText,
        array $extraSkills,
        string $instructions,
        string $language,
        string $tone,
        int $maxTokens,
        string $userName = '',
    ): string {
        $replacements = [
            '{{language}}' => $language,
            '{{tone}}' => $tone,
            '{{max_tokens}}' => (string) $maxTokens,
            '{{job_description}}' => substr($jobDetail->full_description ?? '', 0, 4000),
            '{{technologies}}' => implode(', ', $jobDetail->technologies ?? []),
            '{{cv_text}}' => $cvText,
            '{{extra_skills}}' => ! empty($extraSkills) ? implode(', ', $extraSkills) : 'None specified',
            '{{custom_instructions}}' => $instructions ?: 'None provided',
            '{{user_name}}' => $userName ?: 'the candidate',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), self::INITIAL_SYSTEM_PROMPT);
    }

    private function buildFollowUpPrompt(
        JobDetail $jobDetail,
        string $cvText,
        array $extraSkills,
        string $instructions,
        CoverLetter $previousLetter,
        string $language,
        string $tone,
        int $maxTokens,
        string $userName = '',
    ): string {
        $replacements = [
            '{{language}}' => $language,
            '{{tone}}' => $tone,
            '{{max_tokens}}' => (string) $maxTokens,
            '{{job_description}}' => substr($jobDetail->full_description ?? '', 0, 4000),
            '{{technologies}}' => implode(', ', $jobDetail->technologies ?? []),
            '{{cv_text}}' => $cvText,
            '{{previous_cover_letter}}' => $previousLetter->content ?? '',
            '{{extra_skills}}' => ! empty($extraSkills) ? implode(', ', $extraSkills) : 'None specified',
            '{{custom_instructions}}' => $instructions ?: 'None provided',
            '{{user_name}}' => $userName ?: 'the candidate',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), self::FOLLOW_UP_SYSTEM_PROMPT);
    }

    private function normalizeResult(array $result, JobDetail $jobDetail, array $extraSkills, bool $isFollowUp, string $userName = ''): array
    {
        $normalized = [
            'cover_letter' => $result['cover_letter'] ?? $this->generateFallbackText($jobDetail, $extraSkills, $isFollowUp, $userName),
            'confidence_score' => min(1.0, max(0.0, (float) ($result['confidence_score'] ?? 0.5))),
            'match_reasons' => $result['match_reasons'] ?? [],
        ];

        if ($isFollowUp) {
            $normalized['new_skills_highlighted'] = $result['new_skills_highlighted'] ?? [];
        } else {
            $normalized['matched_technologies'] = $result['matched_technologies'] ?? $jobDetail->technologies ?? [];
        }

        if (! $isFollowUp) {
            $normalized['extra_skills_injected'] = $extraSkills;
        }

        return $normalized;
    }

    private function fallbackResult(JobDetail $jobDetail, array $extraSkills, bool $isFollowUp, string $userName = ''): array
    {
        $result = [
            'cover_letter' => $this->generateFallbackText($jobDetail, $extraSkills, $isFollowUp, $userName),
            'confidence_score' => 0.5,
            'match_reasons' => ['Generated with fallback template'],
        ];

        if ($isFollowUp) {
            $result['new_skills_highlighted'] = $extraSkills;
        } else {
            $result['matched_technologies'] = $jobDetail->technologies ?? [];
            $result['extra_skills_injected'] = $extraSkills;
        }

        return $result;
    }

    private function generateFallbackText(JobDetail $jobDetail, array $extraSkills, bool $isFollowUp, string $userName = ''): string
    {
        $company = $jobDetail->company_name ?? 'the company';
        $title = $jobDetail->jobLink->title ?? 'the position';
        $keyword = $jobDetail->jobLink->keyword->keyword ?? '';

        $skills = ! empty($extraSkills) ? ' Additionally, I bring experience with: '.implode(', ', $extraSkills).'.' : '';

        $signOff = $userName ?: '[Your Name]';

        if ($isFollowUp) {
            return "Dear Hiring Manager,\n\n"
                ."I previously applied for the {$title} position at {$company} and wanted to follow up "
                .'as I noticed the role is still open. I remain very interested in this opportunity '
                ."and am confident in my ability to contribute to your team.{$skills}\n\n"
                .'I would appreciate the opportunity to discuss further how my experience '
                ."aligns with what you are looking for.\n\n"
                ."Thank you for your time and consideration.\n\n"
                ."Best regards,\n{$signOff}";
        }

        return "Dear Hiring Manager,\n\n"
            ."I am writing to apply for the {$title} position at {$company}. "
            ."With my experience in {$keyword} and related technologies, "
            ."I am confident I can contribute significantly to your team.\n\n"
            .'My background aligns well with the requirements of this role. '
            .'I have a proven track record of delivering high-quality work '
            ."and collaborating effectively with cross-functional teams.{$skills}\n\n"
            .'I would welcome the opportunity to discuss how my skills and experience '
            ."can benefit {$company}.\n\n"
            ."Thank you for considering my application.\n\n"
            ."Best regards,\n{$signOff}";
    }
}
