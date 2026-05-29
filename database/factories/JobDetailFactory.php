<?php

namespace Database\Factories;

use App\Models\JobDetail;
use App\Models\JobLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobDetail>
 */
class JobDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_link_id' => JobLink::factory(),
            'full_description' => fake()->paragraphs(5, true),
            'technologies' => fake()->randomElements(['PHP', 'Laravel', 'React', 'Vue.js', 'Docker', 'MySQL', 'Redis', 'TailwindCSS'], rand(2, 5)),
            'salary_from' => fake()->optional()->numberBetween(500, 1500),
            'salary_to' => fake()->optional()->numberBetween(1500, 5000),
            'salary_currency' => 'EUR',
            'company_name' => fake()->company(),
            'contact_email' => fake()->optional()->companyEmail(),
            'recruiter_name' => fake()->optional()->name(),
            'phone' => fake()->optional()->phoneNumber(),
            'requirements' => fake()->randomElements(['PHP experience', 'Laravel knowledge', 'Team player', 'English', 'Problem solving'], rand(2, 4)),
            'responsibilities' => fake()->randomElements(['Write code', 'Review PRs', 'Mentor juniors', 'Design architecture'], rand(1, 3)),
            'seniority' => fake()->randomElement(['junior', 'middle', 'senior', 'lead']),
            'work_type' => fake()->randomElement(['remote', 'hybrid', 'office']),
            'publication_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'reposted' => false,
            'repost_count' => 0,
            'reposted_after_days' => null,
            'similarity_hash' => null,
            'similarity_score' => null,
        ];
    }

    public function reposted(): static
    {
        return $this->state(fn (array $attributes) => [
            'reposted' => true,
            'repost_count' => 1,
            'reposted_after_days' => 8,
        ]);
    }
}
