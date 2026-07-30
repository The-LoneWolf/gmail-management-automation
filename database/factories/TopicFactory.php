<?php

namespace Database\Factories;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Topic>
 */
class TopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['Invoice', 'Support request', 'Sales lead', 'Customer complaint']),
            'slug' => fn (array $attributes): string => Str::slug($attributes['name']).'-'.fake()->unique()->numberBetween(100, 999),
            'description' => fake()->sentence(),
            'examples' => [fake()->sentence()],
            'keywords' => fake()->randomElements(['invoice', 'support', 'lead', 'complaint', 'billing', 'refund'], 2),
            'color' => fake()->hexColor(),
            'minimum_confidence' => 0.85,
            'requires_human_review' => false,
            'is_active' => true,
        ];
    }
}
