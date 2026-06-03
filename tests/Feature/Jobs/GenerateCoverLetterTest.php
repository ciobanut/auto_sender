<?php

use App\Jobs\GenerateCoverLetter;
use App\Models\CoverLetter;
use App\Models\ExtraSkill;
use App\Models\JobDetail;
use App\Models\JobKeyword;
use App\Models\JobLink;
use App\Models\User;
use App\Services\AiService;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->keyword = JobKeyword::factory()->create([
        'user_id' => $this->user->id,
        'keyword' => 'Laravel',
        'ai_instructions' => 'Highlight Laravel experience',
        'cv_path' => null,
    ]);

    $this->jobLink = JobLink::factory()->processed()->create([
        'job_keyword_id' => $this->keyword->id,
    ]);

    $this->jobDetail = JobDetail::factory()->create([
        'job_link_id' => $this->jobLink->id,
        'technologies' => ['PHP', 'Laravel', 'MySQL'],
    ]);
});

test('generates cover letter and creates record', function () {
    // Mock the AiService to return a known result
    $mock = mock(AiService::class)->shouldReceive('generateCoverLetter')
        ->once()
        ->andReturn([
            'cover_letter' => 'Dear Hiring Manager, I am writing to apply...',
            'confidence_score' => 0.92,
            'match_reasons' => ['Strong Laravel experience'],
            'matched_technologies' => ['PHP', 'Laravel', 'MySQL'],
            'extra_skills_injected' => [],
        ]);

    app()->instance(AiService::class, $mock->getMock());

    $job = new GenerateCoverLetter($this->jobLink);
    $job->handle(app(AiService::class));

    $coverLetter = CoverLetter::where('job_link_id', $this->jobLink->id)->first();

    expect($coverLetter)->not->toBeNull()
        ->and($coverLetter->content)->toContain('Dear Hiring Manager')
        ->and($coverLetter->version)->toBe(1)
        ->and($coverLetter->is_follow_up)->toBeFalse()
        ->and($coverLetter->ai_confidence_score)->toBe(0.92)
        ->and($coverLetter->status)->toBe('draft')
        ->and($coverLetter->keyword_id)->toBe($this->keyword->id);
});

test('generates follow-up cover letter for reposted jobs', function () {
    // Mark the job as reposted
    $this->jobDetail->update(['reposted' => true]);

    // Create a previous (initial) cover letter
    CoverLetter::factory()->create([
        'job_link_id' => $this->jobLink->id,
        'job_detail_id' => $this->jobDetail->id,
        'keyword_id' => $this->keyword->id,
        'is_follow_up' => false,
        'version' => 1,
    ]);

    $mock = mock(AiService::class)->shouldReceive('generateCoverLetter')
        ->once()
        ->andReturn([
            'cover_letter' => 'Dear Hiring Manager, I previously applied...',
            'confidence_score' => 0.88,
            'match_reasons' => ['Continued interest'],
            'new_skills_highlighted' => [],
            'extra_skills_injected' => [],
        ]);

    app()->instance(AiService::class, $mock->getMock());

    $job = new GenerateCoverLetter($this->jobLink);
    $job->handle(app(AiService::class));

    $coverLetter = CoverLetter::where('job_link_id', $this->jobLink->id)
        ->where('is_follow_up', true)
        ->first();

    expect($coverLetter)->not->toBeNull()
        ->and($coverLetter->version)->toBe(2)
        ->and($coverLetter->is_follow_up)->toBeTrue();
});

test('includes extra skills in cover letter generation', function () {
    ExtraSkill::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Docker',
        'category' => 'DevOps',
        'sort_order' => 0,
    ]);

    ExtraSkill::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Redis',
        'category' => 'Backend',
        'sort_order' => 1,
    ]);

    $mock = mock(AiService::class)->shouldReceive('generateCoverLetter')
        ->once()
        ->andReturnUsing(function ($jobDetail, $cvText, $extraSkills) {
            expect($extraSkills)->toContain('Docker');
            expect($extraSkills)->toContain('Redis');

            return [
                'cover_letter' => 'Letter with skills',
                'confidence_score' => 0.85,
                'match_reasons' => ['Good match'],
                'matched_technologies' => ['PHP', 'Laravel'],
                'extra_skills_injected' => $extraSkills,
            ];
        });

    app()->instance(AiService::class, $mock->getMock());

    $job = new GenerateCoverLetter($this->jobLink);
    $job->handle(app(AiService::class));
});

test('skips generation when job has no detail or keyword', function () {
    $link = JobLink::factory()->create();
    $link->setRelation('keyword', null);

    $mock = mock(AiService::class)->shouldReceive('generateCoverLetter')->never();
    app()->instance(AiService::class, $mock->getMock());

    $job = new GenerateCoverLetter($link);
    $job->handle(app(AiService::class));

    expect(CoverLetter::count())->toBe(0);
});

test('is dispatched to ai queue', function () {
    Queue::fake();

    GenerateCoverLetter::dispatch($this->jobLink);

    Queue::assertPushed(GenerateCoverLetter::class, function ($job) {
        return $job->queue === null;
    });
});
