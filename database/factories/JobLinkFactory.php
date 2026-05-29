<?php

namespace Database\Factories;

use App\Models\JobKeyword;
use App\Models\JobLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobLink>
 */
class JobLinkFactory extends Factory
{
    public function definition(): array
    {
        $keyword = fake()->randomElement(['php', 'laravel', 'react']);

        return [
            'job_keyword_id' => JobKeyword::factory(),
            'job_url' => fake()->unique()->url().'/job/'.fake()->uuid(),
            'external_job_id' => fake()->optional()->uuid(),
            'title' => fake()->jobTitle(),
            'company_name' => fake()->company(),
            'location' => fake()->city().', Moldova',
            'short_preview' => fake()->optional()->paragraph(),
            'status' => 'new',
            'fetch_count' => 1,
            'first_seen_at' => now(),
            're_fetched_at' => null,
        ];
    }

    public function reFetched(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 're_fetched',
            'fetch_count' => 2,
            're_fetched_at' => now(),
        ]);
    }

    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processed',
        ]);
    }

    public function ignored(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ignored',
        ]);
    }
}
