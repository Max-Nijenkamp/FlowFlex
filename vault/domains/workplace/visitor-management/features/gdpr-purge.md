---
domain: workplace
module: visitor-management
feature: gdpr-purge
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# GDPR Purge

Automatically delete external-visitor PII after the retention window.

## Behaviour

- `PurgeVisitorsCommand` runs daily and deletes visitor rows whose visit is older than 12 months *(assumed retention)*.
- Bounds retention of external personal data (name, email) held for arrival/compliance.
- Already-purged / out-of-window rows are skipped (idempotent).

## UI

- **Kind**: background
- **Trigger**: scheduled console command (`PurgeVisitorsCommand`), daily. No page.

## Data

- Owns / writes: `wp_visitors` (deletes) only.
- Reads: `wp_visitors` (own module).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing.
- Shared entity: none.

> [!warning] UNVERIFIED
> The 12-month window is *(assumed)* pending legal input; whether purge is soft (redact PII, keep aggregate counts) or hard delete is undecided — see [[../unknowns]] and [[../../../../architecture/data-lifecycle]].

## Test Checklist

### Unit
- [ ] Retention predicate: a visit older than 12 months *(assumed)* is in-scope; a fresh one is not.

### Feature (Pest)
- [ ] `PurgeVisitorsCommand` deletes out-of-window rows and leaves in-window rows untouched.
- [ ] Idempotent: a second run purges nothing further (already-purged rows skipped).
- [ ] Purge is `company_id`-scoped — never crosses tenants.

<!-- no Livewire: background console command, no UI -->

## Related

- [[../_module|Visitor Management]] · [[../../../../architecture/data-lifecycle]] · [[../security]]
