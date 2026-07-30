# Phase 5 Through Phase 7 Implementation Notes

## Implemented

- Extraction templates, email extractions, export templates, generated exports, automation rules, automation executions, notification channels, and reply drafts schema.
- Models, casts, relationships, factories, and seed data for the remaining MVP domains.
- Local extraction service behind a deterministic schema-based implementation.
- Export service that writes CSV or JSON files to local storage.
- Automation rule evaluator with basic condition matching.
- Safe automation executor for internal actions and notification markers.
- Restricted automation action gating for risky actions such as email sending, deletion, forwarding, and archiving.
- Reply draft generation service.
- Gmail reply sender that refuses to send unless a draft has been explicitly approved.
- Jobs for extraction, exports, automation evaluation, draft generation, and approved reply sending.
- Filament resources for the remaining management and audit surfaces.
- Tests for extraction, export generation, automation safe/restricted actions, and reply approval gating.

## Safety Boundaries

- Sending replies requires `approved_at` on the reply draft.
- Restricted automation actions create approval-required execution records instead of executing.
- Export generation writes local files only.
- Notification channels are modeled and encrypted, but external delivery is not executed automatically.

## Deferred

- Production OpenAI extraction and draft generation provider.
- XLSX writer UI polish and generated export download actions.
- Full notification delivery implementations for Slack, Telegram, email, and webhooks.
- Approval queue UX for automation executions and reply drafts.
- Fine-grained ownership policies for all resources.
