# Phase 3 and Phase 4 Implementation Notes

## Implemented

- Gmail account scheduler command: `gmail:sync-accounts`.
- Scheduled Gmail sync every five minutes through `routes/console.php`.
- `SyncGmailAccount` dispatches initial sync when no `history_id` exists and history sync otherwise.
- `SyncGmailHistory` and `GmailHistorySyncService` read Gmail history changes and queue changed message imports.
- Topic and state migrations, models, factories, and Filament resources.
- Email classification migration, model, factory, and Filament audit resource.
- `EmailIntelligenceService` interface with deterministic local keyword implementation.
- Classification job and persister that save summaries, topic matches, suggested state, priority, sentiment, reply/review flags, and processing status.
- Improved factories and database seeder for realistic local/demo data.
- `.env.testing` with SQLite in-memory database, array cache/session, sync queue, and test-only app key.

## Deferred

- Real OpenAI provider implementation and structured-output schema validation.
- Gmail 404/expired-history fallback behavior beyond surfacing job failure.
- Attachment download and extracted attachment text.
- AI processing logs and user feedback tables.
- Advanced AI review queue actions.
- Automation rules, notifications, exports, and reply sending.
