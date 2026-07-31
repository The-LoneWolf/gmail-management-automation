# Phase 1 and Phase 2 Implementation Notes

## Implemented

- Laravel latest stable scaffold with Sail.
- Filament admin panel.
- Google API client dependency.
- Gmail OAuth redirect and callback routes.
- Encrypted Gmail account token storage.
- Google client factory with expired-token refresh.
- Gmail account, thread, message, and attachment migrations/models/factories.
- Initial paginated Gmail sync job.
- Full Gmail message import job.
- Gmail parser for headers, labels, recipients, body parts, dates, direction, and attachment metadata.
- Basic Filament resources for Gmail accounts, inbox threads, and inbox messages.
- GitHub Actions test workflow with frontend build coverage.
- GitHub Actions security audit workflow.

## Later Implemented In Follow-Up Phases

- Incremental Gmail history sync and scheduler command.
- Topics, states, local classification, extraction templates, exports, automation rules, notification channels, and reply drafts.
- Approval-gated Gmail reply sending.

## Still Deferred

- Attachment content download and durable storage.
- Production AI providers and structured-output validation.
- External notification delivery.
- True Gmail Pub/Sub push notifications.
