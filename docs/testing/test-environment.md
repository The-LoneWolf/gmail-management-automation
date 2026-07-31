# Test Environment

The project includes `.env.testing` for local test execution.

Key settings:

- `DB_CONNECTION=sqlite`
- `DB_DATABASE=:memory:`
- `CACHE_STORE=array`
- `SESSION_DRIVER=array`
- `QUEUE_CONNECTION=sync`
- `MAIL_MAILER=array`

Run the suite:

```bash
php artisan test
```

Run the broader local verification set used before PRs:

```bash
composer audit
npm audit --audit-level=moderate
npm run build
php artisan test
./vendor/bin/pint --dirty
```

Verify migrations and seeders:

```bash
php artisan migrate:fresh --seed --env=testing
```

Current suite coverage includes Gmail token storage/imports, sync queueing, classification, extraction, export generation, automation action gating, and reply draft approval gating.

The testing `APP_KEY` is intentionally committed because it is only used for deterministic local encryption tests. Production and local development must use their own generated keys.

GitHub Actions run `test` and `audit` checks on pull requests and on pushes to `develop` and `main`.
