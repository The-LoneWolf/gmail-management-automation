# Security Update Agent Command

Use this command when dependency security alerts appear on the repository.

```text
You are handling dependency security alerts for this Laravel/Filament Gmail management repository.

Read docs/operations/security-updates.md and agents/claude.md before changing files.

Tasks:
1. Inspect GitHub Dependabot alerts if repository permissions allow it.
2. Run composer audit and npm audit.
3. Identify each affected package, ecosystem, manifest, vulnerable range, installed version, and patched version.
4. Choose the smallest safe dependency updates that resolve the alerts.
5. Prefer targeted updates:
   - composer update vendor/package --with-dependencies
   - npm update package-name
6. Avoid broad framework, PHP runtime, Node runtime, or toolchain upgrades unless the advisory requires them.
7. Update only the dependency files needed to resolve the advisories.
8. Run:
   - composer audit
   - npm audit
   - npm run build
   - php artisan test
   - ./vendor/bin/pint --dirty
9. Create a short, hyphenated branch from develop, for example security-update-google-client.
10. Summarize changed packages, old and new versions, advisory IDs, verification results, and remaining risk.
11. Create a pull request into develop with a security-focused title and body.

Constraints:
- Do not run destructive git commands.
- Do not suppress advisories without explaining why the application is not affected.
- Do not mix unrelated refactors or feature work into the security update PR.
- If a major version bump is required, explain the breaking-change review performed before opening the PR.
- Do not push directly to develop or main.
```
