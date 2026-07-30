<?php

namespace Database\Factories;

use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<State>
 */
class StateFactory extends Factory
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
            'name' => fake()->randomElement(['New', 'Needs Review', 'Action Required', 'Waiting for Reply', 'Resolved']),
            'slug' => fn (array $attributes): string => Str::slug($attributes['name']).'-'.fake()->unique()->numberBetween(100, 999),
            'description' => fake()->sentence(),
            'type' => 'workflow',
            'sort_order' => fake()->numberBetween(1, 50),
            'color' => fake()->hexColor(),
            'is_initial' => false,
            'is_final' => false,
            'is_active' => true,
        ];
    }
}
