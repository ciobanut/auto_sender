<?php

namespace Database\Factories;

use App\Models\ExtraSkill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExtraSkill>
 */
class ExtraSkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['Docker', 'Kubernetes', 'Redis', 'CI/CD', 'Linux', 'RabbitMQ', 'Webpack', 'Nginx']),
            'category' => fake()->randomElement(['backend', 'frontend', 'devops', 'other']),
            'proficiency' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'sort_order' => 0,
        ];
    }
}
