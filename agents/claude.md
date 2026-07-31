# Agent Implementation Guide

## Project Scope

This project is an AI-assisted Gmail inbox management platform built with Laravel. Current implementation scope is limited to Phase 1 and Phase 2:

- Gmail OAuth connection.
- Encrypted Gmail token storage.
- Gmail account model and migration.
- Google client factory.
- Initial Gmail message import.
- Email thread, message, and attachment storage.
- Queued import jobs.
- Basic Filament inbox.

Do not implement AI classification, automation rules, notifications, exports, or reply sending until the relevant later phase is explicitly started.

## Stack Conventions

- Use the latest stable Laravel available at project creation time.
- Use PHP 8.4 or newer. The current Laravel/Symfony dependency set requires PHP >= 8.4.1.
- Use Laravel Sail for local Docker development.
- Use MySQL and Redis through Sail.
- Use FilamentPHP for authenticated back-office screens.
- Use `google/apiclient` for Gmail API integration.
- Keep Gmail API access isolated behind services under `App\Services\Google` or `App\Services\Gmail`.
- Use queued jobs for imports and remote API calls.
- Prefer idempotent writes with Gmail IDs and database unique constraints.
- Store OAuth tokens using Laravel encrypted casts.
- Use backed PHP enums for constrained domain values when the values appear in migrations and application logic.

## Laravel Boost And AI Agent Tooling

- Laravel Boost is installed as a development-only dependency for Laravel-aware MCP tools, generated guidelines, and agent skills.
- Treat Boost as coding workflow support only. Do not use it as the runtime AI provider layer for email classification, extraction, drafting, or automation.
- Runtime AI integrations should follow `docs/architecture/ai-provider-abstraction.md`, with `laravel/ai` preferred behind the project-owned `AiClient` boundary when that phase starts.
- When Boost MCP is available, use its Laravel documentation search before changing Laravel, Filament, Livewire, Tailwind, Sail, queue, migration, or test code.
- Keep generated Boost files updated with `php artisan boost:install --guidelines --skills --mcp --no-interaction` after dependency upgrades.
- Read `docs/operations/ai-agent-setup.md` before changing agent tooling or generated Boost assets.

## Code Conventions

- Follow Laravel's default directory structure and naming conventions unless the project has an established local convention.
- Keep controllers thin; put Gmail OAuth and sync behavior in services.
- Keep job `handle()` methods small by delegating parsing and API details to services.
- Avoid static helpers for domain logic. Use injectable services.
- Prefer typed method signatures and return types.
- Keep migrations explicit about indexes, uniqueness, nullable fields, and cascade behavior.
- Use factories for model tests where practical.
- Never overwrite an existing Gmail refresh token with `null`.
- Do not send, delete, forward, or archive Gmail messages in Phase 1 or Phase 2.
- Treat imported email content, headers, sender names, and filenames as untrusted data.
- Sanitize HTML before displaying it in admin UI.

## Branch Naming

Use short, lowercase, hyphenated branch names that describe the work clearly. Prefer names that are stable in shells, GitHub URLs, and local git refs.

Recommended prefixes:

- `feature-` for user-facing features, for example `feature-gmail-history-sync`.
- `fix-` for bug fixes, for example `fix-email-preview-images`.
- `chore-` for maintenance, tooling, or repo setup, for example `chore-daily-dependabot`.
- `docs-` for documentation-only changes, for example `docs-release-process`.
- `security-` for security fixes, for example `security-update-google-client`.
- `release-vX.Y.Z` for release preparation branches created by the release workflow.

Branch rules:

- Branch from `develop` for normal feature, fix, docs, chore, and security work.
- Open PRs back into `develop`.
- Do not push directly to `develop` or `main`.
- Use release workflow branches to promote `develop` into `main`.
- Avoid slash-separated branch names such as `chore/daily-dependabot` in this repository; use `chore-daily-dependabot` instead. This avoids local ref conflicts and keeps branch names easy to use across tools.
- Avoid uppercase letters, spaces, underscores, punctuation, ticket-only names, and vague names such as `updates`, `changes`, or `final`.
- Keep branch names under roughly 60 characters.

## Documentation Structure

Use `docs/` for durable project knowledge:

- `docs/architecture/` for system design, data model notes, and service boundaries.
- `docs/implementation/` for phase-by-phase implementation notes.
- `docs/operations/` for local setup, OAuth setup, queues, scheduler, and deployment notes.
- `docs/operations/ai-agent-setup.md` for Laravel Boost, MCP, and generated AI-agent assets.
- `docs/testing/` for test strategy and verification notes.
- `docs/tech-debt/` for known limitations, deferred decisions, and cleanup tasks.

Before implementing a new phase or substantial feature, review the relevant files in `docs/` and update the planned approach if prior decisions or debt affect the work.

After implementing meaningful work, update the docs with:

- What changed.
- What was verified.
- What remains deferred.
- Any new tech debt or follow-up risks.

## Testing Expectations

- Add or update tests for every behavior with meaningful domain risk.
- For Gmail integration, test token persistence, refresh-token preservation, parser behavior, and idempotent import persistence without calling Google over the network.
- Prefer fakes and test doubles around Google API objects.
- Run the relevant test suite before reporting completion.
- Report any tests that could not be run and why.

## Tech Debt Tracking

Record debt in `docs/tech-debt/`. Use one Markdown file per topic or phase. Each entry should include:

- Context.
- Risk.
- Proposed future resolution.
- Phase or milestone where it should be revisited.

Do not silently bury known shortcuts in code comments only.
