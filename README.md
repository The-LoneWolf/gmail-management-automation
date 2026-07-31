# Gmail Management Automation

Laravel and Filament application for connecting Gmail accounts, importing messages, previewing email content, classifying messages, extracting structured data, evaluating automation rules, drafting replies, and generating exports.

The current Gmail sync is a queued import and history polling workflow. It is not a full real-time Gmail push notification mirror yet.

## Stack

- PHP 8.4
- Laravel 13
- Filament 5
- Laravel Sail
- MySQL 8.4
- Redis
- Database-backed queues
- Google API PHP client
- Node.js 22 or newer
- Vite and Tailwind
- PHPUnit 12

## Features

- Google OAuth configuration from the Filament dashboard or `.env`
- Gmail account connection via OAuth
- Encrypted Gmail token and Google client secret storage
- Initial Gmail import
- Gmail history sync every five minutes when the scheduler is running
- Manual "Sync latest" action in Email Messages
- Email threads, messages, and attachments
- Safe HTML email preview with remote images/fonts allowed and scripts blocked
- Keyword-based local classification
- Topics and workflow states
- Extraction templates, export templates, automation rules, notification channels, reply drafts
- Factories, seeders, test environment, and GitHub Actions test workflow

## Requirements

- Docker Desktop or another Docker-compatible runtime
- Composer
- Node.js 22 or newer and npm
- Git

Sail is the recommended runtime because this project targets PHP 8.4.

## Fresh Setup

Clone the repository and install PHP dependencies:

```bash
git clone https://github.com/The-LoneWolf/gmail-management-automation.git
cd gmail-management-automation
composer install
```

Create the local environment file:

```bash
cp .env.example .env
```

For Sail/MySQL development, set these values in `.env`:

```dotenv
APP_NAME="Gmail Management Automation"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

Start Sail and prepare the app:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Open the app at:

```text
http://localhost/admin
```

Seeded local admin login:

```text
Email: admin@example.com
Password: password
```

## Daily Development

Start the containers:

```bash
./vendor/bin/sail up -d
```

Run the frontend dev server:

```bash
./vendor/bin/sail npm run dev
```

Run the queue worker:

```bash
./vendor/bin/sail artisan queue:work
```

Run the scheduler locally if you want automatic Gmail history polling:

```bash
./vendor/bin/sail artisan schedule:work
```

The scheduler runs:

```text
gmail:sync-accounts every five minutes
```

You can also queue Gmail syncs manually:

```bash
./vendor/bin/sail artisan gmail:sync-accounts
./vendor/bin/sail artisan gmail:sync-accounts --account=1
```

## Google OAuth Setup

You can configure Google OAuth in the dashboard:

```text
Admin > Settings > Google OAuth Setup
```

Click **Create**, then use the **Setup steps** button on that page for the detailed Google Cloud walkthrough.

High-level Google Cloud steps:

1. Open Google Cloud Console: https://console.cloud.google.com/
2. Create or select a project.
3. Enable Gmail API: https://console.cloud.google.com/apis/library/gmail.googleapis.com
4. Configure Google Auth Platform: https://console.cloud.google.com/auth/overview
5. Add your Gmail address as a test user while the app is in Testing mode.
6. Add OAuth scopes at https://console.cloud.google.com/auth/scopes
7. Create a Web application OAuth client at https://console.cloud.google.com/auth/clients
8. Add this authorized redirect URI:

```text
http://localhost/gmail/oauth/callback
```

Required scopes:

```text
openid
email
profile
https://www.googleapis.com/auth/gmail.modify
```

Copy the Google Client ID and Client secret into the dashboard record, save it, then go to:

```text
Admin > Gmail Accounts > Connect Gmail
```

### Environment-Based OAuth

The dashboard OAuth setup is preferred for local use because it avoids editing `.env` repeatedly. You can also configure OAuth with environment variables:

```dotenv
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/gmail/oauth/callback"
GOOGLE_GMAIL_SCOPES="openid,email,profile,https://www.googleapis.com/auth/gmail.modify"
```

After changing `.env`, clear cached config:

```bash
./vendor/bin/sail artisan optimize:clear
```

## Gmail Import Behavior

When a Gmail account is connected, `InitialGmailSync` queues an initial import. The default initial import cap is currently 100 messages.

Additional messages can be pulled by:

- Clicking **Sync latest** in Email Messages
- Running `gmail:sync-accounts`
- Running the scheduler, which polls Gmail history every five minutes

The current history sync listens for:

- `messageAdded`
- `labelAdded`
- `labelRemoved`

This updates new messages and label-driven state such as read/unread, starred, inbox, and archived after the next sync. True Gmail push notifications, Pub/Sub webhooks, full deletion mirroring, and long-running backfills are future work.

## Classification

Classification is currently local and keyword-based. The job name is `ClassifyEmailWithAi`, but the service binding uses `KeywordEmailIntelligenceService`.

Configure classification in:

```text
Admin > Classification > Topics
Admin > Classification > States
```

Topics control category matching through keywords. States control workflow status. The current classifier treats these state slugs specially:

```text
new
needs-review
action-required
```

Changing topics affects new classifications. Existing messages are not automatically reclassified yet.

## Testing

Run the full test suite:

```bash
php artisan test
```

Or through Sail:

```bash
./vendor/bin/sail artisan test
```

Run formatting:

```bash
./vendor/bin/pint
```

Useful focused checks:

```bash
php artisan test --filter=GoogleOAuth
php artisan test --filter=EmailMessagePreviewTest
```

The test environment uses SQLite in memory, array sessions, and synchronous queues. See `docs/testing/test-environment.md`.

## Branch and Release Workflow

This repository uses protected `develop` and `main` branches:

- Normal work branches start from `develop` and open PRs back into `develop`.
- `develop` requires PRs plus passing `test` and `audit` checks. It uses 0 approvals for solo-owner merging.
- `main` is the release branch and stays stricter. Release PRs are opened from the `Prepare Release` workflow.
- Branch names should be short, lowercase, hyphenated names such as `feature-gmail-history-sync`, `fix-email-preview-images`, or `chore-daily-dependabot`.
- Avoid slash-separated branch names in this repository.

Release flow:

1. Merge feature/fix/security PRs into `develop`.
2. Run GitHub Actions > `Prepare Release` with a semantic version such as `1.0.0`.
3. Review and merge the generated release PR into `main`.
4. `Publish Release` creates the tag and GitHub Release from `main`.

See `docs/operations/release-process.md` and `agents/release-manager.md`.

## GitHub Actions

The repository includes:

- `.github/workflows/tests.yml`
- `.github/workflows/security.yml`
- `.github/workflows/prepare-release.yml`
- `.github/workflows/publish-release.yml`

The test workflow runs on pushes to `main` and `develop`, and on pull requests. It:

- Checks out the repo
- Installs PHP 8.4
- Installs Composer dependencies
- Installs Node.js 22
- Installs npm dependencies with `npm ci`
- Builds frontend assets
- Copies `.env.example`
- Generates an app key
- Runs the PHPUnit suite with SQLite in memory

The security workflow runs on pushes to `main` and `develop`, pull requests, a daily schedule, and manual dispatch. It runs Composer and npm audits.

Dependabot checks Composer and npm daily and assigns opened PRs to `The-LoneWolf`.

## Troubleshooting

### Missing `sessions` Table

If you see:

```text
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'laravel.sessions' doesn't exist
```

Run migrations:

```bash
./vendor/bin/sail artisan migrate
```

### Google OAuth Missing `client_id`

If Google shows `Missing required parameter: client_id`, create an active Google OAuth Setup record in the admin dashboard or configure `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` in `.env`, then run:

```bash
./vendor/bin/sail artisan optimize:clear
```

### Google OAuth Invalid Scope

Use the exact Gmail scope:

```text
https://www.googleapis.com/auth/gmail.modify
```

Do not use `gmail.modify` without the full URL.

### Queue Jobs Are Not Running

Start a queue worker:

```bash
./vendor/bin/sail artisan queue:work
```

Check failed jobs:

```bash
./vendor/bin/sail artisan queue:failed
```

Retry failed jobs:

```bash
./vendor/bin/sail artisan queue:retry all
```

## Project Documentation

Project docs are in `docs/`:

- `docs/README.md`
- `docs/architecture/`
- `docs/implementation/`
- `docs/operations/`
- `docs/testing/`
- `docs/tech-debt/`

Security update workflow:

```text
docs/operations/security-updates.md
agents/security-update.md
```

Release workflow:

```text
docs/operations/release-process.md
agents/release-manager.md
```

Agent and contribution conventions are in:

```text
agents/claude.md
```

Branch naming conventions are documented in `agents/claude.md`.

## Security Notes

- Gmail access tokens, refresh tokens, and Google OAuth client secrets are encrypted by Laravel casts.
- Email previews allow images and fonts but block scripts.
- Reply sending and restricted automation actions are intentionally gated.
- Do not commit real OAuth credentials or production secrets.

## License

This project is open-source under the MIT license.
