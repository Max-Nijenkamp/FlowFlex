---
domain: foundation
module: laravel-scaffold
feature: soft-delete-lifecycle
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Soft-Delete Lifecycle

Every model soft-deletes (`deleted_at`) rather than hard-deleting — recoverable removal, the baseline for audit and GDPR erasure.

## Behaviour

- All models use `SoftDeletes`; queries auto-exclude trashed rows.
- Hard erasure (true `forceDelete`) is reserved for GDPR/DSAR cascades owned by [[../../../../security/data-privacy-gdpr|data-privacy]] — the scaffold only provides the mechanism.
- `company` cascade on `users` is a real FK `cascadeOnDelete` at the DB level; company soft-delete vs. hard-delete cascade behaviour is domain-decided (data-privacy).
- Enforced by arch test: `ModelsTest` asserts `SoftDeletes` on models.

## UI

- **Kind**: background. Restore/purge screens, when they exist, belong to the owning domain (e.g. audit-log,
  data-privacy), not here.

## Data

- Owns: no tables (adds `deleted_at` to every table). Cross-domain writes: none.

## Relations

- Consumes: nothing. Feeds: [[../../../../security/data-privacy-gdpr]] erasure flows, [[../../../../domains/core/audit-log/_module|audit-log]] rely on soft-delete semantics.

## Unknowns

> [!warning] UNVERIFIED — company deletion cascade semantics (soft vs. force, and whether child rows purge)
> live in the data-privacy domain, not confirmed here.

## Related

- [[../_module|Laravel Scaffold]] · [[../data-model]] · [[../../../../security/data-privacy-gdpr]]
