# Phase 1 and Phase 2 Verification

## Automated Coverage

- Token encryption at rest.
- Refresh-token preservation when Google returns no new refresh token.
- Gmail parser header/body/label/attachment extraction.
- HTML sanitization for basic unsafe tags and inline handlers.
- Idempotent message import by Gmail message ID.

## Test Command

```bash
php artisan test
```

Tests use SQLite in memory through `.env.testing` and `phpunit.xml`, so Docker is not required for the automated suite.

For changes that affect frontend assets, CI, dependencies, or shared application behavior, also run:

```bash
composer audit
npm audit --audit-level=moderate
npm run build
./vendor/bin/pint --dirty
```
