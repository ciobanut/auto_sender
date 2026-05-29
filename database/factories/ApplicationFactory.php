<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\CoverLetter;
use App\Models\JobKeyword;
use App\Models\JobLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_link_id' => JobLink::factory(),
            'cover_letter_id' => CoverLetter::factory(),
            'keyword_id' => JobKeyword::factory(),
            'sent_at' => now(),
            'delivery_status' => 'pending',
            'response_received' => false,
            'response_at' => null,
            'response_type' => null,
            'recruiter_reply_text' => null,
            'follow_up_sent' => false,
            'follow_up_at' => null,
            'notes' => null,
        ];
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_status' => 'delivered',
        ]);
    }

    public function withResponse(string $type = 'interview'): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_status' => 'delivered',
            'response_received' => true,
            'response_at' => now()->addDays(rand(1, 14)),
            'response_type' => $type,
            'recruiter_reply_text' => fake()->optional()->paragraph(),
        ]);
    }
}
