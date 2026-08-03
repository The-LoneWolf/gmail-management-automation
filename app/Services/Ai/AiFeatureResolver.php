<?php

namespace App\Services\Ai;

use App\Enums\AiFeature;
use App\Models\AiFeatureSetting;
use RuntimeException;

class AiFeatureResolver
{
    public function resolve(AiFeature|string $feature): AiFeatureSetting
    {
        $featureValue = $feature instanceof AiFeature ? $feature->value : $feature;

        $setting = AiFeatureSetting::query()
            ->with(['provider', 'model.provider'])
            ->where('feature', $featureValue)
            ->where('is_enabled', true)
            ->first();

        if (! $setting) {
            throw new RuntimeException("No enabled AI feature setting exists for [{$featureValue}].");
        }

        if (! $setting->provider->is_active) {
            throw new RuntimeException("The AI provider for [{$featureValue}] is inactive.");
        }

        if (! $setting->model->is_active) {
            throw new RuntimeException("The AI model for [{$featureValue}] is inactive.");
        }

        if ($setting->model->ai_provider_id !== $setting->provider->id) {
            throw new RuntimeException("The AI model selected for [{$featureValue}] does not belong to the selected provider.");
        }

        if ($setting->requires_tools && ! $setting->model->supports_tool_calling) {
            throw new RuntimeException("The AI model selected for [{$featureValue}] does not support tool calling.");
        }

        if ($setting->requires_vision && ! $setting->model->supports_vision) {
            throw new RuntimeException("The AI model selected for [{$featureValue}] does not support vision.");
        }

        return $setting;
    }
}
