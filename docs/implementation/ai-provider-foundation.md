# AI Provider Foundation

Date: 2026-07-31

This phase adds dashboard-managed AI provider and model configuration. It does not yet call providers from classification, extraction, automation, or reply features.

## What Exists

- `ai_providers` stores external AI endpoints and gateway settings.
- `ai_models` stores provider model IDs, endpoint overrides, capability flags, and token limits.
- Provider API keys are encrypted.
- Secret headers are encrypted.
- Filament Settings contains `AI Providers` and `AI Models`.
- `AI Providers` includes an idempotent `Install OpenCode Mimo preset` action.

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

## Boundaries

This foundation intentionally does not:

- Run AI classification or extraction.
- Send prompts to external AI APIs.
- Create reply drafts with an AI provider.
- Give AI permission to change Gmail messages.

Runtime use should be added through an app-owned AI client contract so features can select a configured provider/model and tests can fake outbound calls.
