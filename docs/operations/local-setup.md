# Local Setup

## Sail

The current dependency set requires PHP >= 8.4.1 and Node.js >= 22. Sail is the recommended local PHP runtime.

Install dependencies and start the app:

```bash
composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

The Sail stack uses MySQL and Redis from `compose.yaml`.

The seeded local admin account is:

```text
Email: admin@example.com
Password: password
```

For frontend work, run:

```bash
./vendor/bin/sail npm run dev
```

## Gmail OAuth

Create OAuth credentials in Google Cloud Console.

You can configure the credentials in the Filament dashboard under **Settings > Google OAuth Setup**. The client secret is stored encrypted in the database.

Alternatively, configure `.env`:

```dotenv
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/gmail/oauth/callback"
GOOGLE_GMAIL_SCOPES="openid,email,profile,https://www.googleapis.com/auth/gmail.modify"
```

The redirect URI must match the Google OAuth client exactly.

For local Sail development with `APP_URL=http://localhost`, add this Authorized redirect URI to the Google OAuth client:

```text
http://localhost/gmail/oauth/callback
```

After editing `.env`, clear cached configuration:

```bash
./vendor/bin/sail artisan optimize:clear
```

If Google shows `Missing required parameter: client_id`, the app is still missing a dashboard Google OAuth setup record or `GOOGLE_CLIENT_ID`, or it is serving stale cached config.

## Queues

Gmail imports are queued. During development run:

```bash
./vendor/bin/sail artisan queue:work
```

For automatic Gmail history polling, run the scheduler:

```bash
./vendor/bin/sail artisan schedule:work
```

The scheduler queues `gmail:sync-accounts` every five minutes.

## Test Environment

Run local tests without Docker:

```bash
php artisan test
php artisan migrate:fresh --seed --env=testing
```

The normal CI-equivalent local verification set is:

```bash
composer audit
npm audit --audit-level=moderate
npm run build
php artisan test
./vendor/bin/pint --dirty
```
