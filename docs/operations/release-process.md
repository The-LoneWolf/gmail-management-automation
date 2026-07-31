# Release Process

This repository uses two protected long-lived branches:

- `develop`: integration branch for approved feature, fix, and maintenance PRs.
- `main`: release branch. Production builds and GitHub Releases are created from this branch.

## Normal Development Flow

1. Create a feature branch from `develop`.
2. Open a PR back into `develop`.
3. Wait for required CI checks.
4. Review and approve the PR.
5. Merge into `develop`.

Do not push directly to `develop` or `main`.

## Release Flow

1. Go to GitHub Actions.
2. Run the `Prepare Release` workflow.
3. Enter the version, for example `1.2.0`.
4. The workflow creates `release/v1.2.0`, updates `VERSION`, updates `CHANGELOG.md`, and opens a PR into `main`.
5. Review the release PR.
6. Merge the release PR into `main`.
7. The `Publish Release` workflow creates tag `v1.2.0` and publishes the GitHub Release.

If the repository has GitHub auto-merge enabled, the `Prepare Release` workflow can enable auto-merge on the release PR. The PR still waits for required checks and approvals before merging.

## Branch Protection

Configure these settings in GitHub:

```text
Repository > Settings > Branches > Add branch protection rule
```

For `develop`:

- Require a pull request before merging.
- Require 0 approvals for solo-owner repositories.
- Require status checks to pass before merging.
- Require branches to be up to date before merging.
- Block force pushes.
- Block deletions.

This keeps `develop` protected from direct pushes while allowing the repository owner to merge their own PRs after checks pass. If more maintainers are added later, raise this to at least 1 approval.

For `main`:

- Require a pull request before merging.
- Require at least 1 approval.
- Dismiss stale pull request approvals when new commits are pushed.
- Require status checks to pass before merging.
- Require branches to be up to date before merging.
- Restrict who can push.
- Block force pushes.
- Block deletions.

Recommended required checks:

- `test`
- `audit`

If GitHub shows workflow job names with the workflow prefix, select the exact visible job names from the branch protection UI.

## Changelog Rules

The release workflow updates `CHANGELOG.md` from commit subjects between the latest tag and `develop`.

Use clear commit prefixes when possible:

- `feat:` for features.
- `fix:` for bug fixes.
- `security:` for security fixes.
- `deps:` for dependency updates.
- `docs:` for documentation changes.

## Emergency Fixes

For urgent production fixes:

1. Branch from `main`.
2. Open a hotfix PR into `main`.
3. After release, merge `main` back into `develop`.

Document why the normal `develop` release flow was bypassed.
