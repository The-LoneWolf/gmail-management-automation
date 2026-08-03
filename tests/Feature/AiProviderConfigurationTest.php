<?php

namespace Tests\Feature;

use App\Enums\AiFeature;
use App\Models\AiFeatureSetting;
use App\Models\AiModel;
use App\Models\AiProvider;
use App\Services\Ai\AiFeatureResolver;
use App\Services\Ai\AiProviderPresetService;
use App\Services\Ai\ChatCompletionsClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;
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

    public function test_feature_resolver_uses_database_selected_provider_and_model(): void
    {
        $provider = AiProvider::factory()->create([
            'name' => 'Local 9router',
            'endpoint_url' => 'https://ai.example.test/v1/chat/completions',
        ]);
        $model = AiModel::factory()->for($provider, 'provider')->create([
            'provider_model_id' => 'my-dynamic-model',
            'supports_tool_calling' => true,
        ]);
        AiFeatureSetting::factory()->for($provider, 'provider')->for($model, 'model')->create([
            'feature' => AiFeature::EmailClassification,
            'requires_tools' => true,
        ]);

        $setting = app(AiFeatureResolver::class)->resolve(AiFeature::EmailClassification);

        $this->assertTrue($setting->provider->is($provider));
        $this->assertTrue($setting->model->is($model));
        $this->assertSame('my-dynamic-model', $setting->model->provider_model_id);
    }

    public function test_chat_completions_client_calls_selected_model_endpoint(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'ai.example.test/*' => Http::response([
                'id' => 'chatcmpl-test',
                'choices' => [
                    ['message' => ['role' => 'assistant', 'content' => 'classified']],
                ],
            ]),
        ]);

        $provider = AiProvider::factory()->create([
            'endpoint_url' => 'https://ai.example.test/v1/chat/completions',
            'api_key' => 'dynamic-provider-key',
            'secret_headers' => ['X-Router' => 'tenant-a'],
            'default_body' => ['stream' => false],
            'timeout_seconds' => 12,
            'retry_attempts' => 0,
        ]);
        $model = AiModel::factory()->for($provider, 'provider')->create([
            'provider_model_id' => 'custom-dynamic-model',
            'endpoint_url' => null,
            'max_output_tokens' => 2048,
        ]);
        AiFeatureSetting::factory()->for($provider, 'provider')->for($model, 'model')->create([
            'feature' => AiFeature::EmailClassification,
            'temperature' => 0.40,
            'system_prompt' => 'Classify email.',
            'request_overrides' => ['response_format' => ['type' => 'json_object']],
        ]);

        $response = app(ChatCompletionsClient::class)->send(AiFeature::EmailClassification, [
            ['role' => 'user', 'content' => 'Email body'],
        ]);

        $this->assertSame('chatcmpl-test', $response['id']);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();

            return $request->url() === 'https://ai.example.test/v1/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer dynamic-provider-key')
                && $request->hasHeader('X-Router', 'tenant-a')
                && $data['model'] === 'custom-dynamic-model'
                && $data['messages'][0] === ['role' => 'system', 'content' => 'Classify email.']
                && $data['messages'][1] === ['role' => 'user', 'content' => 'Email body']
                && $data['temperature'] === 0.4
                && $data['max_tokens'] === 2048
                && $data['stream'] === false
                && $data['response_format'] === ['type' => 'json_object'];
        });
    }

    public function test_feature_resolver_rejects_model_from_a_different_provider(): void
    {
        $selectedProvider = AiProvider::factory()->create();
        $otherProvider = AiProvider::factory()->create();
        $model = AiModel::factory()->for($otherProvider, 'provider')->create();

        AiFeatureSetting::factory()->for($selectedProvider, 'provider')->for($model, 'model')->create([
            'feature' => AiFeature::EmailExtraction,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not belong to the selected provider');

        app(AiFeatureResolver::class)->resolve(AiFeature::EmailExtraction);
    }
}
