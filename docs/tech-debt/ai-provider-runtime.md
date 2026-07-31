# AI Provider Runtime Tech Debt

Date: 2026-07-31

The dashboard can now store AI providers and model capabilities, including the OpenCode Mimo preset. The runtime client contract is still missing.

## Follow-Up Work

- Add an internal AI client contract that accepts a configured `AiProvider` and `AiModel`.
- Decide whether Laravel AI SDK is the first runtime adapter, with raw HTTP as the fallback for custom endpoints.
- Add provider validation without sending sensitive email content.
- Add per-feature AI settings for classification, extraction, automation conditions, reply drafts, and summaries.
- Add request audit logs with status, latency, tokens, model, provider, and sanitized error category.
- Keep Gmail-destructive actions behind explicit policy and approval gates.
