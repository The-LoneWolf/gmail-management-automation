# Release Manager Agent Command

Use this command when preparing a release from `develop` to `main`.

```text
You are preparing a repository release.

Read docs/operations/release-process.md and agents/claude.md before changing files.

Tasks:
1. Confirm the working tree is clean.
2. Confirm the release version is semantic, for example 1.2.0.
3. Confirm develop contains the commits intended for release.
4. Run the relevant checks:
   - composer audit
   - npm audit
   - npm run build
   - php artisan test
   - ./vendor/bin/pint --dirty
5. Use the Prepare Release GitHub Action when possible.
6. If preparing locally, create release-vVERSION from develop, update VERSION and CHANGELOG.md, and open a PR into main.
7. Do not push directly to main.
8. Do not bypass branch protections.
9. Summarize included changes, verification results, and release risks.

Constraints:
- Do not run destructive git commands.
- Do not mix unrelated feature work into release PRs.
- Do not create a release tag before the release PR is merged into main.
- Keep main stricter than develop; main release PRs require review.
```
