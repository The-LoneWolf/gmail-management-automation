# Security Update Workflow

Use this workflow when GitHub, Dependabot, Composer, npm, or an AI agent reports dependency security alerts.

## First Response

1. Create a branch from latest `main`.
2. Identify the affected ecosystem, package, installed version, vulnerable range, and patched version.
3. Prefer the narrowest safe update that reaches the patched version.
4. Avoid broad framework or toolchain upgrades unless the advisory requires them.
5. Run the relevant audit and test commands before opening a PR.

## Check GitHub Dependabot Alerts

If GitHub CLI has the required repository permission:

```bash
gh api repos/OWNER/REPO/dependabot/alerts --paginate \
  --jq '.[] | select(.state == "open") | {number, package: .dependency.package.name, ecosystem: .dependency.package.ecosystem, manifest: .dependency.manifest_path, severity: .security_advisory.severity, vulnerable: .security_vulnerability.vulnerable_version_range, patched: .security_vulnerability.first_patched_version.identifier, advisory: .security_advisory.ghsa_id}'
```

For this repository:

```bash
gh api repos/The-LoneWolf/gmail-management-automation/dependabot/alerts --paginate \
  --jq '.[] | select(.state == "open") | {number, package: .dependency.package.name, ecosystem: .dependency.package.ecosystem, manifest: .dependency.manifest_path, severity: .security_advisory.severity, vulnerable: .security_vulnerability.vulnerable_version_range, patched: .security_vulnerability.first_patched_version.identifier, advisory: .security_advisory.ghsa_id}'
```

If the API says alerts are disabled, enable repository security features in GitHub:

```text
Repository > Settings > Code security and analysis
```

Enable:

- Dependency graph
- Dependabot alerts
- Dependabot security updates

## Composer Security Updates

Audit current PHP dependencies:

```bash
composer audit
```

Show outdated direct dependencies:

```bash
composer outdated --direct
```

Update one vulnerable package and its related dependencies:

```bash
composer update vendor/package --with-dependencies
```

When a Laravel package requires framework-aligned updates, update the smallest related set:

```bash
composer update vendor/package laravel/framework filament/filament --with-dependencies
```

Verify:

```bash
composer audit
php artisan test
./vendor/bin/pint --dirty
```

## npm Security Updates

Audit current JavaScript dependencies:

```bash
npm audit
```

Apply non-breaking fixes first:

```bash
npm audit fix
```

If the fix requires a major version update, inspect the advisory and package release notes before using:

```bash
npm audit fix --force
```

For targeted updates:

```bash
npm update package-name
```

Verify:

```bash
npm audit
npm run build
php artisan test
```

## PR Requirements

Every security update PR should include:

- Advisory or alert ID
- Affected package and ecosystem
- Old version and new version
- Why the chosen update is the smallest safe fix
- Commands run and results
- Any remaining alerts or risks

## AI Agent Prompt

Use this prompt when asking an AI coding agent to handle security alerts:

```text
There are dependency security alerts on this repository.

Please:
1. Inspect GitHub Dependabot alerts if available.
2. Run composer audit and npm audit.
3. Identify the affected packages, vulnerable version ranges, and patched versions.
4. Choose the narrowest dependency updates that fix the alerts without broad framework/toolchain upgrades.
5. Update composer.lock, package-lock.json, or manifests only as needed.
6. Run formatting, build, audits, and tests.
7. Document what changed, what was verified, and any residual risk.
8. Create a PR with a concise security-focused title and body.

Do not use destructive git commands.
Do not update unrelated dependencies unless required by the dependency solver.
Do not suppress or ignore advisories without documenting why the app is not affected.
```

## Local Status From Initial Setup

At the time this workflow was added:

- `composer audit --format=json` reported no Composer advisories.
- `npm install --package-lock-only` created `package-lock.json`.
- `npm audit --json` reported no npm vulnerabilities.
- GitHub Dependabot alerts API returned that alerts were disabled for the repository.
