# Phase 1 and Phase 2 Tech Debt

## Attachment Downloads

Context: Phase 2 stores attachment metadata only.

Risk: Later AI extraction and export features need actual attachment files and extracted text.

Resolution: Add `DownloadGmailAttachment` and storage rules before extraction templates.

## HTML Sanitization

Context: Email previews now use a safer preview endpoint that allows remote images/fonts and blocks scripts. Parser-level safeguards still exist for imported content.

Risk: Rich HTML email rendering remains a high-risk surface because email HTML is hostile input.

Resolution: Keep preview rendering sandboxed, add stronger sanitizer policy tests as supported HTML coverage grows, and avoid allowing scripts.

## Authorization Policies

Context: Filament authentication is enabled, but fine-grained policies for multi-user email access are not yet implemented.

Risk: Future shared/team features need stricter authorization boundaries.

Resolution: Add Gmail account, thread, message, and attachment policies before team/shared inbox work.
