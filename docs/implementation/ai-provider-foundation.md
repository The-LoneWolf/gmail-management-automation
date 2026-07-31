# AI Provider Foundation

Date: 2026-07-31

This phase adds dashboard-managed AI provider and model configuration. It does not yet call providers from classification, extraction, automation, or reply features.

## What Exists

- `ai_providers` stores external AI endpoints and gateway settings.
- `ai_models` stores provider model IDs, endpoint overrides, capability flags, and token limits.
- `ai_feature_settings` maps product features to a selected provider/model pair.
- Provider API keys are encrypted.
- Secret headers are encrypted.
- Filament Settings contains `AI Providers`, `AI Models`, and `AI Feature Settings`.
- `AI Providers` includes an idempotent `Install OpenCode Mimo preset` action.
- `AiFeatureResolver` resolves the active provider/model for a feature.
- `ChatCompletionsClient` sends OpenAI-compatible chat-completions requests using the selected provider/model.

## OpenCode Mimo Preset

The preset creates this provider/model shape:

```json
{
  "name": "https://opencode.ai/zen/v1/chat/completions",
  "vendor": "customendpoint",
  "apiType": "chat-completions",
  "models": [
    {
      "id": "mimo-v2.5-free",
      "name": "mimo-v2.5-free",
      "url": "https://opencode.ai/zen/v1/chat/completions",
      "toolCalling": true,
      "vision": true,
      "maxInputTokens": 200000,
      "maxOutputTokens": 32000
    }
  ]
}
```

## How To Configure

1. Open `/admin/ai-providers`.
2. Click `Install OpenCode Mimo preset`, or create a provider manually.
3. If the endpoint requires authentication, edit the provider and add an API key or encrypted secret headers.
4. Open `/admin/ai-models` to adjust model IDs, endpoint overrides, capability flags, and token limits.
5. Open `/admin/ai-feature-settings`.
6. Create or edit a feature setting, such as `Email classification`.
7. Select the provider and model that feature should use.

## Runtime Usage

Application services should not hardcode model names. Resolve the configured feature and call the generic client:

```php
use App\Enums\AiFeature;
use App\Services\Ai\ChatCompletionsClient;

$response = app(ChatCompletionsClient::class)->send(AiFeature::EmailClassification, [
    ['role' => 'user', 'content' => 'Classify this email...'],
]);
```

The client reads these values from the database:

- endpoint URL from the selected model override or provider endpoint
- provider API key
- encrypted custom headers
- model ID
- feature temperature
- model or feature output token limit
- provider default body
- feature request overrides

This means Mimo, OpenRouter, LiteLLM, 9router, or another OpenAI-compatible gateway can be selected without changing the feature code.

## Boundaries

This foundation intentionally does not:

- Run AI classification or extraction.
- Create reply drafts with an AI provider.
- Give AI permission to change Gmail messages.

The generic chat-completions client exists, but the existing product workflows still need separate integration work before they use it.
