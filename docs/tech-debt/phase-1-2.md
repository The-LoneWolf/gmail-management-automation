# Phase 1 and Phase 2 Tech Debt

## Gmail History Sync

Context: Initial import stores the latest imported message `history_id`, but does not use Gmail history API yet.

Risk: Ongoing sync cannot efficiently detect changed labels or new messages.

Resolution: Implement Phase 3 with `users.history.list`, scheduler command, and sync status/error visibility.

## Attachment Downloads

Context: Phase 2 stores attachment metadata only.

Risk: Later AI extraction and export features need actual attachment files and extracted text.

Resolution: Add `DownloadGmailAttachment` and storage rules before extraction templates.

## HTML Sanitization

Context: The parser strips scripts and inline event handlers as a first-pass safeguard.

Risk: Rich HTML email rendering needs a stronger allowlist sanitizer and remote-image handling.

Resolution: Replace with a dedicated sanitizer policy before rendering full HTML bodies broadly in Filament.

## Authorization Policies

Context: Filament authentication is enabled, but fine-grained policies for multi-user email access are not yet implemented.

Risk: Future shared/team features need stricter authorization boundaries.

Resolution: Add Gmail account, thread, message, and attachment policies before team/shared inbox work.
