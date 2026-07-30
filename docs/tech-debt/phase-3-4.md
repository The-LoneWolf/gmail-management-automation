# Phase 3 and Phase 4 Tech Debt

## Gmail History Expiration

Context: Gmail can reject a stored `history_id` when it is too old.

Risk: Incremental sync may fail until the account is re-imported.

Resolution: Detect expired history errors and queue a bounded full sync fallback in Phase 5 or before production use.

## Local Keyword Classifier

Context: Phase 4 uses a deterministic keyword classifier behind `EmailIntelligenceService`.

Risk: It is useful for tests and local workflow validation but is not a substitute for a structured AI provider.

Resolution: Add `OpenAiEmailIntelligenceService` with JSON schema validation before relying on classification quality.

## Classification Reprocessing

Context: Imported messages are classified on first import, but there is no Filament action for manual reprocessing yet.

Risk: Topic/state changes will not automatically refresh old classifications.

Resolution: Add a reprocess action and stale-classification detection when the review queue is implemented.

## Policies

Context: New topic, state, and classification resources are available in Filament, but fine-grained policies remain deferred.

Risk: Multi-user deployments need enforced ownership checks.

Resolution: Add policies before shared/team usage.
