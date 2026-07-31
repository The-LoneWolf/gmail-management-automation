<?php

namespace Database\Factories;

use App\Enums\AiFeature;
use App\Models\AiFeatureSetting;
use App\Models\AiModel;
use App\Models\AiProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiFeatureSetting>
 */
class AiFeatureSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $provider = AiProvider::factory();

        return [
            'feature' => AiFeature::EmailClassification,
            'name' => 'Email classification',
            'ai_provider_id' => $provider,
            'ai_model_id' => AiModel::factory()->for($provider, 'provider'),
            'temperature' => 0.20,
            'max_output_tokens' => null,
            'system_prompt' => null,
            'request_overrides' => null,
            'requires_json' => false,
            'requires_tools' => false,
            'requires_vision' => false,
            'is_enabled' => true,
        ];
    }
}
