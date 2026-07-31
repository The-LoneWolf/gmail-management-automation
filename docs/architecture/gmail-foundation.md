# Gmail Foundation Architecture

## Scope

The foundation started with Gmail connectivity and local inbox storage. The current system now includes Gmail history polling, local classification, extraction/export scaffolding, automation evaluation, notification channel modeling, and approval-gated reply sending.

Remote Gmail mutation, external notification delivery, production AI providers, full attachment download/storage, and true Gmail Pub/Sub push notifications remain intentionally gated or deferred.

## Boundaries

- `App\Services\Google\GoogleClientFactory` owns Google client creation and access-token refresh.
- `App\Services\Gmail\GmailMessageParser` converts Gmail API message payloads into safe local arrays.
- `App\Services\Gmail\GmailImportService` owns idempotent persistence for threads, messages, and attachment metadata.
- `InitialGmailSync` queues message imports from paginated Gmail message IDs.
- `ImportGmailMessage` fetches one full Gmail message and persists it.
- `gmail:sync-accounts` and scheduled jobs poll Gmail history every five minutes when the scheduler is running.
- Classification, extraction, export, automation, notification, and reply-draft domains live behind local deterministic services and explicit safety gates.

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
- Classification topics, states, and message classification audit records.
- Extraction/export templates and generated exports.
- Automation rules/executions, notification channels, and reply drafts.

Email sending and restricted automation actions require explicit approval paths.
