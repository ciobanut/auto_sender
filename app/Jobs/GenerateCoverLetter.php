<?php

namespace App\Jobs;

use App\Models\AiSetting;
use App\Models\CoverLetter;
use App\Models\ExtraSkill;
use App\Models\JobLink;
use App\Services\AiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateCoverLetter implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobLink $jobLink,
    ) {
        $this->onQueue('ai');
    }

    public function handle(AiService $aiService): void
    {
        $detail = $this->jobLink->detail;
        $keyword = $this->jobLink->keyword;

        if (! $detail || ! $keyword) {
            return;
        }

        // Load CV text
        $cvText = $this->loadCvText($keyword);

        // Load extra skills for this user
        $extraSkills = ExtraSkill::where('user_id', $keyword->user_id)
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();

        // Load AI settings
        $settings = $this->loadAiSettings($keyword->user_id);

        // Determine if this is a follow-up (repost)
        $isFollowUp = $detail->reposted;

        // Find previous cover letter if this is a follow-up
        $previousLetter = null;
        if ($isFollowUp) {
            $previousLetter = CoverLetter::where('job_link_id', $this->jobLink->id)
                ->where('is_follow_up', false)
                ->latest()
                ->first();
        }

        // Generate cover letter via AI service
        $result = $aiService->generateCoverLetter(
            jobDetail: $detail,
            cvText: $cvText,
            extraSkills: $extraSkills,
            instructions: $keyword->ai_instructions ?? '',
            previousLetter: $previousLetter,
            settings: $settings,
        );

        // Store the result
        CoverLetter::create([
            'job_link_id' => $this->jobLink->id,
            'job_detail_id' => $detail->id,
            'keyword_id' => $keyword->id,
            'content' => $result['cover_letter'],
            'version' => $isFollowUp ? 2 : 1,
            'is_follow_up' => $isFollowUp,
            'ai_model' => $settings['model'] ?? 'gpt-4o-mini',
            'ai_confidence_score' => $result['confidence_score'],
            'match_explanation' => implode("\n", $result['match_reasons']),
            'extra_skills_injected' => $result['extra_skills_injected'] ?? [],
            'status' => 'draft',
        ]);
    }

    private function loadCvText($keyword): string
    {
        if (! $keyword->cv_path) {
            return 'No CV provided.';
        }

        $disk = Storage::disk('cvs');
        if (! $disk->exists($keyword->cv_path)) {
            return 'No CV provided.';
        }

        $content = $disk->get($keyword->cv_path);

        // Extract text from PDF if possible
        if (strtolower(pathinfo($keyword->cv_path, PATHINFO_EXTENSION)) === 'pdf') {
            return $this->extractPdfText($content);
        }

        // For text files, return as-is
        return mb_substr($content, 0, 5000);
    }

    private function extractPdfText(string $pdfContent): string
    {
        // Basic PDF text extraction using a simple approach.
        // For production, consider using a dedicated PDF parser like smalot/pdf-parser.
        $text = preg_replace('/[\r\n]+/', "\n", $pdfContent);

        // Remove PDF metadata and garbage
        $text = preg_replace('/obj\s*<<.*?>>/s', '', $text);
        $text = preg_replace('/stream.*?endstream/s', '', $text);

        // Extract text between parentheses (PDF text objects)
        preg_match_all('/\(([^)]*)\)/', $text, $matches);
        $content = implode(' ', $matches[1] ?? []);

        // Decode PDF escape sequences
        $content = preg_replace('/\\\\(\d{3})/', function ($m) {
            return chr((int) octdec($m[1]));
        }, $content);

        $content = trim(preg_replace('/\s+/', ' ', $content));

        return mb_substr($content ?: 'No extractable text found in PDF.', 0, 5000);
    }

    private function loadAiSettings(int $userId): array
    {
        $aiSetting = AiSetting::where('user_id', $userId)->first();

        if (! $aiSetting) {
            return [];
        }

        return [
            'model' => $aiSetting->model,
            'temperature' => $aiSetting->temperature,
            'max_tokens' => $aiSetting->max_tokens,
            'language' => $aiSetting->language,
            'tone' => $aiSetting->tone,
        ];
    }
}
