# AI Provider Runtime Tech Debt

Date: 2026-07-31

The dashboard can now store AI providers, model capabilities, and feature-level provider/model selection. A generic chat-completions client exists for OpenAI-compatible endpoints, including the OpenCode Mimo preset. Existing product workflows still need to be wired to that client deliberately.

## Follow-Up Work

- Add a richer internal AI client contract if the simple `ChatCompletionsClient` becomes too narrow.
- Decide whether Laravel AI SDK should replace or wrap the raw HTTP chat-completions client.
- Add provider validation without sending sensitive email content.
- Wire email classification to `AiFeature::EmailClassification` with prompt versions and strict output validation.
- Wire extraction, automation conditions, reply drafts, and summaries to their feature settings.
- Add request audit logs with status, latency, tokens, model, provider, and sanitized error category.
- Keep Gmail-destructive actions behind explicit policy and approval gates.
