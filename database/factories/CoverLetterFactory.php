<?php

namespace Database\Factories;

use App\Models\CoverLetter;
use App\Models\JobDetail;
use App\Models\JobKeyword;
use App\Models\JobLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CoverLetter>
 */
class CoverLetterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_link_id' => JobLink::factory(),
            'job_detail_id' => JobDetail::factory(),
            'keyword_id' => JobKeyword::factory(),
            'content' => fake()->paragraphs(3, true),
            'version' => 1,
            'is_follow_up' => false,
            'ai_model' => 'deepseek-v4-flash',
            'ai_confidence_score' => fake()->randomFloat(2, 0.6, 0.98),
            'match_explanation' => fake()->sentence(),
            'extra_skills_injected' => fake()->randomElements(['Docker', 'Redis', 'CI/CD'], rand(0, 3)),
            'editable_content' => null,
            'status' => 'draft',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function followUp(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_follow_up' => true,
            'version' => 2,
        ]);
    }
}
