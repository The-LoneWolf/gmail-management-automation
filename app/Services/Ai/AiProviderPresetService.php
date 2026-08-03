<?php

namespace App\Services\Ai;

use App\Models\AiProvider;

class AiProviderPresetService
{
    public const OPENCODE_MIMO_ENDPOINT = 'https://opencode.ai/zen/v1/chat/completions';

    public const OPENCODE_MIMO_MODEL = 'mimo-v2.5-free';

    public function upsertOpenCodeMimo(): AiProvider
    {
        $provider = AiProvider::query()->updateOrCreate(
            [
                'vendor' => 'customendpoint',
                'api_type' => 'chat-completions',
                'endpoint_url' => self::OPENCODE_MIMO_ENDPOINT,
            ],
            [
                'name' => 'OpenCode Zen',
                'timeout_seconds' => 60,
                'retry_attempts' => 2,
                'is_active' => true,
            ],
        );

        $provider->models()->updateOrCreate(
            ['provider_model_id' => self::OPENCODE_MIMO_MODEL],
            [
                'name' => self::OPENCODE_MIMO_MODEL,
                'endpoint_url' => self::OPENCODE_MIMO_ENDPOINT,
                'supports_tool_calling' => true,
                'supports_vision' => true,
                'supports_streaming' => true,
                'max_input_tokens' => 200000,
                'max_output_tokens' => 32000,
                'metadata' => [
                    'preset' => 'opencode-mimo-v2.5-free',
                    'source' => 'dashboard',
                ],
                'is_active' => true,
            ],
        );

        return $provider->load('models');
    }
}
