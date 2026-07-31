# Documentation Index

This directory stores durable project knowledge for operators, maintainers, and AI coding agents.

## Architecture

- `docs/architecture/gmail-foundation.md`: current Gmail, sync, storage, UI, and safety boundaries.
- `docs/architecture/ai-provider-abstraction.md`: researched design for dashboard-managed AI provider credentials, model routing, adapters, and safety boundaries.

## Implementation Notes

- `docs/implementation/phase-1-2.md`: Gmail OAuth, account storage, message import, and inbox foundation.
- `docs/implementation/phase-3-4.md`: Gmail history sync, classification, states, topics, seeders, and test environment.
- `docs/implementation/phase-5-7.md`: extraction, exports, automation, notifications, reply drafts, and approval gates.
- `docs/implementation/ai-provider-foundation.md`: dashboard-managed AI providers, AI models, and the OpenCode Mimo preset.

## Operations

- `docs/operations/local-setup.md`: Sail setup, OAuth setup, queues, scheduler, and local verification commands.
- `docs/operations/ai-agent-setup.md`: Laravel Boost, MCP, generated agent assets, and AI-agent workflow.
- `docs/operations/security-updates.md`: Dependabot, security alerts, audits, owner notifications, and security-update PR process.
- `docs/operations/release-process.md`: protected branch model, release PR flow, changelog behavior, and GitHub Release publishing.

## Testing

- `docs/testing/test-environment.md`: `.env.testing`, local test setup, and CI-aligned verification commands.
- `docs/testing/phase-1-2-verification.md`: focused verification notes for Gmail foundation behavior.

## Tech Debt

- `docs/tech-debt/phase-1-2.md`: foundation risks and follow-up work.
- `docs/tech-debt/phase-3-4.md`: history sync, classifier, reprocessing, and policy follow-up work.
- `docs/tech-debt/phase-5-7.md`: external providers, Filament UX, notifications, and reply-sending follow-up work.
- `docs/tech-debt/ai-provider-runtime.md`: AI runtime client, feature settings, validation, and audit logging follow-up work.

## Agent Commands

Agent-facing instructions live in `agents/`:

- `agents/claude.md`: project conventions, branch naming, docs expectations, tests, and debt tracking.
- `agents/security-update.md`: dependency security update workflow.
- `agents/release-manager.md`: release preparation workflow.

Update this index whenever a new durable documentation area is added.
