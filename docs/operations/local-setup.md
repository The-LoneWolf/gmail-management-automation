# Local Setup

## Sail

The current dependency set requires PHP >= 8.4.1. Sail is the recommended local runtime.

Install dependencies and start the app:

```bash
composer install
npm install
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

The Sail stack uses MySQL and Redis from `compose.yaml`.

## Gmail OAuth

Create OAuth credentials in Google Cloud Console and configure:

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

If Google shows `Missing required parameter: client_id`, the app is still missing `GOOGLE_CLIENT_ID` or is serving stale cached config.

## Queues

Gmail imports are queued. During development run:

```bash
./vendor/bin/sail artisan queue:work
```

## Test Environment

Run local tests without Docker:

```bash
php artisan test
php artisan migrate:fresh --seed --env=testing
```
