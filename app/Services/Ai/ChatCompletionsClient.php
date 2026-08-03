<?php

namespace App\Services\Ai;

use App\Enums\AiFeature;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class ChatCompletionsClient
{
    public function __construct(private readonly AiFeatureResolver $resolver) {}

    /**
     * @param  array<int, array{role: string, content: mixed}>  $messages
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function send(AiFeature|string $feature, array $messages, array $overrides = []): array
    {
        $setting = $this->resolver->resolve($feature);
        $provider = $setting->provider;
        $model = $setting->model;

        $payload = array_replace_recursive(
            $provider->default_body ?? [],
            $setting->request_overrides ?? [],
            [
                'model' => $model->provider_model_id,
                'messages' => $this->messagesWithSystemPrompt($messages, $setting->system_prompt),
                'temperature' => (float) $setting->temperature,
                'max_tokens' => $setting->max_output_tokens ?? $model->max_output_tokens,
            ],
            $overrides,
        );

        $payload = Arr::where($payload, fn (mixed $value): bool => $value !== null);

        $request = Http::timeout($provider->timeout_seconds)
            ->connectTimeout(min(10, $provider->timeout_seconds))
            ->acceptJson()
            ->asJson();

        if ($provider->retry_attempts > 0) {
            $request = $request->retry($provider->retry_attempts, 250);
        }

        if ($provider->api_key) {
            $request = $request->withToken($provider->api_key);
        }

        if ($provider->secret_headers) {
            $request = $request->withHeaders($provider->secret_headers);
        }

        /** @var Response $response */
        $response = $request->post($model->effectiveEndpointUrl(), $payload)->throw();

        return $response->json();
    }

    /**
     * @param  array<int, array{role: string, content: mixed}>  $messages
     * @return array<int, array{role: string, content: mixed}>
     */
    private function messagesWithSystemPrompt(array $messages, ?string $systemPrompt): array
    {
        if (! filled($systemPrompt)) {
            return $messages;
        }

        return [
            ['role' => 'system', 'content' => $systemPrompt],
            ...$messages,
        ];
    }
}
