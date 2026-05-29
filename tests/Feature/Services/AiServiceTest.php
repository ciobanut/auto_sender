<?php

use App\Models\CoverLetter;
use App\Models\JobDetail;
use App\Models\JobLink;
use App\Services\AiService;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\Meta\MetaInformation;

beforeEach(function () {
    Cache::flush();

    $jobLink = JobLink::factory()->processed()->create();
    $this->jobDetail = JobDetail::factory()->create([
        'job_link_id' => $jobLink->id,
        'full_description' => 'We are looking for a PHP Laravel developer with MySQL experience.',
        'technologies' => ['PHP', 'Laravel', 'MySQL'],
        'company_name' => 'Test Corp',
    ]);

    $this->cvText = 'Experienced PHP developer with 5 years of Laravel experience.';
    $this->extraSkills = ['Docker', 'Redis'];
    $this->instructions = 'Highlight my Laravel experience.';
});

function fakeOpenAiResponse(array $jsonContent): CreateResponse
{
    return CreateResponse::from([
        'id' => 'chatcmpl-test',
        'object' => 'chat.completion',
        'created' => 1677652288,
        'model' => 'gpt-4o-mini',
        'system_fingerprint' => 'fp_test',
        'choices' => [
            [
                'index' => 0,
                'message' => [
                    'role' => 'assistant',
                    'content' => json_encode($jsonContent),
                ],
                'finish_reason' => 'stop',
            ],
        ],
        'usage' => [
            'prompt_tokens' => 100,
            'completion_tokens' => 50,
            'total_tokens' => 150,
        ],
    ], MetaInformation::from([]));
}

test('generates cover letter from OpenAI response', function () {
    OpenAI::fake([
        fakeOpenAiResponse([
            'cover_letter' => 'Dear Hiring Manager, I am writing to apply...',
            'confidence_score' => 0.92,
            'match_reasons' => ['Strong Laravel experience', 'PHP background matches'],
            'matched_technologies' => ['PHP', 'Laravel', 'MySQL'],
        ]),
    ]);

    $service = app(AiService::class);
    $result = $service->generateCoverLetter(
        jobDetail: $this->jobDetail,
        cvText: $this->cvText,
        extraSkills: $this->extraSkills,
        instructions: $this->instructions,
    );

    expect($result)
        ->toHaveKey('cover_letter')
        ->toHaveKey('confidence_score')
        ->toHaveKey('match_reasons')
        ->toHaveKey('matched_technologies')
        ->toHaveKey('extra_skills_injected');

    expect($result['cover_letter'])->toBeString()->not->toBeEmpty();
    expect($result['confidence_score'])->toBe(0.92);
    expect($result['match_reasons'])->toContain('Strong Laravel experience');
});

test('generates follow-up cover letter for reposted jobs', function () {
    $previousLetter = CoverLetter::factory()->create([
        'job_link_id' => $this->jobDetail->jobLink->id,
        'content' => 'Original application letter...',
        'is_follow_up' => false,
    ]);

    OpenAI::fake([
        fakeOpenAiResponse([
            'cover_letter' => 'Dear Hiring Manager, I previously applied...',
            'confidence_score' => 0.88,
            'new_skills_highlighted' => ['Docker'],
            'match_reasons' => ['Continued interest'],
        ]),
    ]);

    $service = app(AiService::class);
    $result = $service->generateCoverLetter(
        jobDetail: $this->jobDetail,
        cvText: $this->cvText,
        extraSkills: $this->extraSkills,
        instructions: $this->instructions,
        previousLetter: $previousLetter,
    );

    expect($result)
        ->toHaveKey('cover_letter')
        ->toHaveKey('confidence_score')
        ->toHaveKey('match_reasons')
        ->toHaveKey('new_skills_highlighted');

    expect($result['confidence_score'])->toBe(0.88);
    expect($result['new_skills_highlighted'])->toContain('Docker');
});

test('returns fallback result when OpenAI fails', function () {
    OpenAI::fake([
        fakeOpenAiResponse(['invalid' => 'no cover letter key']),
    ]);

    $service = app(AiService::class);
    $result = $service->generateCoverLetter(
        jobDetail: $this->jobDetail,
        cvText: $this->cvText,
        extraSkills: $this->extraSkills,
        instructions: $this->instructions,
    );

    expect($result)
        ->toHaveKey('cover_letter')
        ->toHaveKey('confidence_score')
        ->toHaveKey('match_reasons');
    expect($result['cover_letter'])->toBeString()->not->toBeEmpty();
});

test('returns fallback when OpenAI throws exception', function () {
    // Passing a Throwable to OpenAI::fake() will make it throw
    OpenAI::fake([
        new Exception('API connection failed'),
    ]);

    $service = app(AiService::class);
    $result = $service->generateCoverLetter(
        jobDetail: $this->jobDetail,
        cvText: $this->cvText,
        extraSkills: $this->extraSkills,
        instructions: $this->instructions,
    );

    expect($result)
        ->toHaveKey('cover_letter')
        ->toHaveKey('confidence_score')
        ->toHaveKey('match_reasons');
    expect($result['confidence_score'])->toBe(0.5);
    expect($result['cover_letter'])->toContain('Dear Hiring Manager');
});

test('caches AI responses', function () {
    $response = fakeOpenAiResponse([
        'cover_letter' => 'Cached letter content',
        'confidence_score' => 0.9,
        'match_reasons' => ['Good match'],
        'matched_technologies' => ['PHP'],
    ]);

    OpenAI::fake([$response, $response]);

    $service = app(AiService::class);

    $result1 = $service->generateCoverLetter(
        jobDetail: $this->jobDetail,
        cvText: $this->cvText,
        extraSkills: $this->extraSkills,
        instructions: $this->instructions,
    );

    $result2 = $service->generateCoverLetter(
        jobDetail: $this->jobDetail,
        cvText: $this->cvText,
        extraSkills: $this->extraSkills,
        instructions: $this->instructions,
    );

    expect($result2['cover_letter'])->toBe($result1['cover_letter']);
    expect($result2['confidence_score'])->toBe($result1['confidence_score']);
});

test('extracts technologies from description using AI', function () {
    OpenAI::fake([
        fakeOpenAiResponse([
            'technologies' => ['PHP', 'Laravel', 'MySQL'],
            'seniority' => 'Senior',
            'work_type' => 'remote',
            'salary_mentioned' => true,
            'salary_from' => 2000,
            'salary_to' => 4000,
        ]),
    ]);

    $service = app(AiService::class);
    $technologies = $service->extractTechnologies('PHP Laravel developer needed. MySQL experience required.');

    expect($technologies)->toBeArray()->not->toBeEmpty();
    expect($technologies)->toContain('PHP');
    expect($technologies)->toContain('Laravel');
});
