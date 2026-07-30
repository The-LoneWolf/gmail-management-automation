# Phase 5 Through Phase 7 Tech Debt

## External Providers

Context: Extraction and draft generation use deterministic local implementations.

Risk: Local implementations are useful for product workflow and tests but not sufficient for real AI-assisted production behavior.

Resolution: Add OpenAI-backed services with structured output validation and provider-level audit logs.

## Filament UX

Context: Remaining MVP resources exist, but several audit screens are generated/basic.

Risk: Operators can inspect data, but higher-quality approval/reprocess/download workflows still need dedicated actions.

Resolution: Add custom Filament actions for export download, automation approval, reply approval, and reprocessing.

## Notification Delivery

Context: Notification channels store encrypted configuration and automation can mark channel use, but external delivery is not implemented.

Risk: Rules do not yet send real Slack, Telegram, email, or webhook notifications.

Resolution: Add a `NotificationChannelService` with provider-specific drivers and tests.

## Reply Sending

Context: Gmail send is implemented behind approval, but there is no editing/approval workflow polish yet.

Risk: A user needs a safer UI flow before real production sending.

Resolution: Add approval policy checks, edit history, and clear send confirmation in Filament.
