<?php

namespace Database\Factories;

use App\Models\AiProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiProvider>
 */
class AiProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' AI',
            'vendor' => 'customendpoint',
            'api_type' => 'chat-completions',
            'endpoint_url' => fake()->url().'/v1/chat/completions',
            'api_key' => 'test-api-key',
            'secret_headers' => null,
            'default_body' => null,
            'timeout_seconds' => 60,
            'retry_attempts' => 2,
            'is_active' => true,
        ];
    }
}
