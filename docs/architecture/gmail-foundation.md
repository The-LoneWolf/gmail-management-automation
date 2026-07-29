# Gmail Foundation Architecture

## Scope

Phase 1 and Phase 2 establish Gmail connectivity and local inbox storage only. AI classification, automation, notifications, exports, and reply sending are intentionally deferred.

## Boundaries

- `App\Services\Google\GoogleClientFactory` owns Google client creation and access-token refresh.
- `App\Services\Gmail\GmailMessageParser` converts Gmail API message payloads into safe local arrays.
- `App\Services\Gmail\GmailImportService` owns idempotent persistence for threads, messages, and attachment metadata.
- `InitialGmailSync` queues message imports from paginated Gmail message IDs.
- `ImportGmailMessage` fetches one full Gmail message and persists it.

## Storage Rules

- Gmail OAuth tokens are stored with Laravel encrypted casts.
- Existing refresh tokens are preserved when Google does not return a new one.
- Gmail accounts are unique by `user_id` and `google_email`.
- Email messages are unique by `gmail_account_id` and `gmail_message_id`.
- Email threads are unique by `gmail_account_id` and `gmail_thread_id`.
- Attachment metadata is unique by `email_message_id` and `gmail_attachment_id`.

## UI

Filament exposes:

- Gmail account status and a connect action.
- Inbox threads.
- Inbox messages.

The inbox is read-oriented in this phase because records should originate from Gmail imports.
