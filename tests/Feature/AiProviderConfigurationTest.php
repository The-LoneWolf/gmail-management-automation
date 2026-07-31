<?php

namespace Tests\Feature;

use App\Models\AiProvider;
use App\Services\Ai\AiProviderPresetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AiProviderConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_key_and_secret_headers_are_encrypted_at_rest(): void
    {
        $provider = AiProvider::factory()->create([
            'api_key' => 'plain-ai-key',
            'secret_headers' => ['X-Gateway-Key' => 'plain-header-secret'],
        ]);

        $raw = DB::table('ai_providers')->where('id', $provider->id)->first();

        $this->assertNotSame('plain-ai-key', $raw->api_key);
        $this->assertStringNotContainsString('plain-header-secret', $raw->secret_headers);
        $this->assertSame('plain-ai-key', $provider->fresh()->api_key);
        $this->assertSame(['X-Gateway-Key' => 'plain-header-secret'], $provider->fresh()->secret_headers);
    }

    public function test_opencode_mimo_preset_is_idempotent_and_keeps_capabilities(): void
    {
        $service = app(AiProviderPresetService::class);

        $provider = $service->upsertOpenCodeMimo();
        $service->upsertOpenCodeMimo();

        $this->assertDatabaseCount('ai_providers', 1);
        $this->assertDatabaseCount('ai_models', 1);

        $model = $provider->fresh('models')->models->first();

        $this->assertSame('OpenCode Zen', $provider->fresh()->name);
        $this->assertSame('customendpoint', $provider->vendor);
        $this->assertSame('chat-completions', $provider->api_type);
        $this->assertSame('mimo-v2.5-free', $model->provider_model_id);
        $this->assertTrue($model->supports_tool_calling);
        $this->assertTrue($model->supports_vision);
        $this->assertSame(200000, $model->max_input_tokens);
        $this->assertSame(32000, $model->max_output_tokens);
    }

    public function test_provider_configuration_matches_opencode_custom_endpoint_shape(): void
    {
        $provider = app(AiProviderPresetService::class)->upsertOpenCodeMimo();

        $this->assertSame([
            'name' => 'https://opencode.ai/zen/v1/chat/completions',
            'vendor' => 'customendpoint',
            'apiType' => 'chat-completions',
            'models' => [
                [
                    'id' => 'mimo-v2.5-free',
                    'name' => 'mimo-v2.5-free',
                    'url' => 'https://opencode.ai/zen/v1/chat/completions',
                    'toolCalling' => true,
                    'vision' => true,
                    'maxInputTokens' => 200000,
                    'maxOutputTokens' => 32000,
                ],
            ],
        ], $provider->toProviderConfiguration());
    }
}
