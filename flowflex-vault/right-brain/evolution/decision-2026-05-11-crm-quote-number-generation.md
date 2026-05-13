---
type: adr
date: 2026-05-11
status: decided
color: "#F97316"
---

# Decision: CRM Quote numbers use uniqid suffix in Phase 3; sequential numbering deferred

## Context

The `crm_quotes` table has a `number` string column. During Phase 3, a proper sequential quote number (e.g. `Q-2026-001`) would require either a DB sequence, a company-scoped counter table, or a locked SELECT MAX()+1 query — none of which were within scope of the initial data-layer build.

## Options Considered

1. **DB sequence (PostgreSQL)** — clean, collision-free, but requires raw SQL in migrations and a custom Eloquent accessor. Overhead for Phase 3.
2. **Counter table** — generic `company_counters` table with a lock. Clean pattern but adds a new table with no other immediate use.
3. **Uniqid suffix (`Q-XXXXXX`)** — simple, zero-DB-overhead, unique within the session. Not perfectly sequential.
4. **Skip auto-generation; make number a required form field** — pushes burden to user, poor UX.

## Decision

Option 3: `Q-` + 6-char uppercase `uniqid()` suffix, generated in `CreateCrmQuote::mutateFormDataBeforeCreate`. Zero infrastructure overhead, ships working in Phase 3.

When `CrmQuoteService` is built in a later session, it will take over number generation with a company-scoped sequence.

## Consequences

- Quote numbers are unique but not sequential (e.g. `Q-8F3A2C` not `Q-2026-001`)
- Users can manually enter a number in the form if they want a specific value (field is not read-only)
- A future `CrmQuoteService::generateNumber()` will supersede this with sequential numbering
- No data migration needed — existing uniqid numbers will remain valid

## Related Left Brain

- [[quotes-proposals]]
- [[builder-log-crm-phase3]]
