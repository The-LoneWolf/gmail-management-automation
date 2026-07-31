<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\AiProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiModel>
 */
class AiModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ai_provider_id' => AiProvider::factory(),
            'provider_model_id' => fake()->slug(2),
            'name' => fake()->words(2, true),
            'endpoint_url' => null,
            'supports_tool_calling' => fake()->boolean(),
            'supports_vision' => fake()->boolean(),
            'supports_streaming' => true,
            'max_input_tokens' => fake()->numberBetween(8192, 200000),
            'max_output_tokens' => fake()->numberBetween(1024, 32000),
            'metadata' => null,
            'is_active' => true,
        ];
    }
}
