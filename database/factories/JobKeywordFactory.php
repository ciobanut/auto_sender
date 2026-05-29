<?php

namespace Database\Factories;

use App\Models\JobKeyword;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobKeyword>
 */
class JobKeywordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'keyword' => fake()->randomElement(['PHP', 'Laravel', 'React', 'WordPress', 'DevOps', 'Vue.js']),
            'cv_path' => null,
            'ai_instructions' => fake()->optional()->sentence(),
            'auto_apply_enabled' => fake()->boolean(),
            'cooldown_hours' => 720,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withAutoApply(): static
    {
        return $this->state(fn (array $attributes) => [
            'auto_apply_enabled' => true,
        ]);
    }
}
