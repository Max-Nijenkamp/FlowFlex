---
type: adr
date: 2026-07-02
status: decided
domain: All
color: "#F97316"
---

# Optimistic Locking — Platform Standard for Concurrent Edits

## Context

The 2026-07-02 full-vault audit found concurrent-edit protection **almost entirely undocumented**. Many modules share editable records across surfaces (a deal edited from the resource form while the pipeline board moves it; an employee profile edited by HR while self-service updates an address; an invoice edited while a payment posts). Two writers on one record silently produce last-write-wins data loss.

What existed before this ADR:
- [[architecture/patterns/states]] — pessimistic `lockForUpdate()` inside `DB::transaction()` for state-machine transitions only.
- Ad-hoc atomic guards in a few domains (event registration capacity, room-booking overlap, scheduled-export double-send).
- `dms_document_locks` — explicit checkout locks for DMS versioning.
- **No** general story for ordinary CRUD: no version column, no `updated_at` stale-check, no conflict UX.

## Options Considered

1. **Optimistic locking (stale-check on save)** — Chosen. Forms carry the record's `updated_at` when loaded; save compares against current DB value; mismatch aborts with a "record changed" conflict notification. No blocking, no lock lifetime management, zero cost in the happy path, works for every Filament form and API PATCH.
2. **Pessimistic locks on edit** — Rejected as the default. Blocks the second editor for the whole edit session, needs lock expiry/steal UX, punishes the common case (no conflict) for the rare one.
3. **Do nothing / last-write-wins** — Rejected. Silent data loss across 172 modules with heavily linked domains is exactly the failure the audit flagged.

## Decision

**Three-tier concurrency model — every module spec must state which tier each write path uses** (new mandatory `## Concurrency` note in `architecture.md`, per [[decisions/decision-2026-07-02-spec-template-v3-exploded-format]]):

| Tier | Mechanism | Applies to |
|---|---|---|
| **Optimistic (default)** | `updated_at` stale-check on save → `StaleRecordException` → "This record was changed by someone else. Review the changes and retry." notification with a **Reload record** action | All ordinary CRUD edits — Filament forms, Livewire actions, API `PATCH`/`PUT` |
| **Pessimistic** | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write | State-machine transitions ([[architecture/patterns/states]]), money mutations (payments, journal postings, payroll), inventory/capacity decrements (stock, tickets, room slots) |
| **Document locks** | Explicit checkout/checkin rows (`dms_document_locks`) | DMS versioned documents only |

Rules:
1. The stale-check uses `updated_at` (already on every table) — no new version column *(a dedicated `lock_version` integer may be introduced later by ADR if `updated_at` second-precision collisions ever surface)*.
2. Conflict is **never silent**: the second writer always sees the conflict notification; the first write always survives.
3. Cross-domain writes are unaffected — they already go through events (single writer per table, [[security/data-ownership]]).
4. Full mechanism, Filament wiring, exception class, and testing recipe: [[architecture/patterns/optimistic-locking]] (new pattern doc, authorised by this ADR).

## Consequences

- New pattern doc [[architecture/patterns/optimistic-locking]]; `/flowflex:patterns locking` key added to CLAUDE.md.
- Every module `architecture.md` gains a `## Concurrency` note (backfilled by the 2026-07 propagation waves).
- [[architecture/way-of-working]] DoD gains a concurrency item; test checklists gain a stale-write case for modules with shared editable records.
- `StaleRecordException` joins the platform exception set ([[architecture/error-handling]]).

## Related

- [[architecture/patterns/optimistic-locking]]
- [[architecture/patterns/states]]
- [[architecture/error-handling]]
- [[decisions/decision-2026-07-02-spec-template-v3-exploded-format]]
