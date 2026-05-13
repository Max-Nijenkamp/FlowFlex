---
type: adr
date: 2026-05-12
status: decided
color: "#F97316"
---

# Decision: ULID id for ordering; withoutGlobalScopes() must not bypass SoftDeletes in assertions

Two related patterns discovered during Phase 6-8 test stabilization.

---

## Context

Two test failures surfaced structural issues with how we query and assert on time-ordered and soft-deleted records.

---

## Decision 1: Use `orderByDesc('id')` not `orderByDesc('created_at')` for ULID-keyed models

### Problem
`AuditService::getCompanyLog()` ordered by `created_at DESC`. Two records inserted in the same test run within the same second have identical `created_at` timestamps — PostgreSQL returns them in an unspecified order, making test assertions non-deterministic.

### Options
1. `orderByDesc('created_at')` — second-precision, non-deterministic for same-second inserts
2. `orderByDesc('id')` — ULID PKs embed millisecond-precision monotonic timestamps, always sortable

### Decision
Use `orderByDesc('id')` for all "latest first" queries on ULID-keyed models. ULID is lexicographically sortable by insertion time with millisecond precision — no ties.

### Consequences
- All service methods returning "recent first" should use `orderByDesc('id')` as primary sort
- `created_at` can still be used for date-range filters, just not for ordering
- Applies to: AuditService, any future event-log or time-series service

---

## Decision 2: Never use `withoutGlobalScopes()->find()` in test assertions for soft-deleted records

### Problem
`withoutGlobalScopes()` removes ALL Eloquent global scopes, including the SoftDeletes scope. A soft-deleted record will still be returned by `withoutGlobalScopes()->find($id)`. Two tests expected `null` after soft delete but got the model object.

### Options
1. `Model::find($id)` — respects SoftDeletes scope, returns null for soft-deleted records (correct for asserting deletion)
2. `Model::withoutGlobalScopes()->find($id)` — bypasses SoftDeletes, finds soft-deleted records (wrong for asserting deletion)
3. `Model::withTrashed()->find($id)->trashed()` — explicitly checks soft-deleted state

### Decision
Use `Model::find($id)` (no `withoutGlobalScopes`) in test assertions that check a record is "gone" after a soft-delete operation. The company scope is always set in `beforeEach`, so the company global scope works correctly.

Use `Model::withoutGlobalScopes()->find($id)` only when you need to bypass the company scope (not soft-delete scope).

### Consequences
- All test assertions for `deleteComment()`, `deallocate()`, soft-delete-based "remove" operations must use `find()` not `withoutGlobalScopes()->find()`
- If a future test needs to verify soft-delete state explicitly, use `->withTrashed()->find()->trashed()` and assert `toBeTrue()`

---

## Related Left Brain

- [[builder-log-phase6-7-8-test-stabilization]]
- `app/Services/Analytics/AuditService.php`
- `tests/Feature/Projects/CollaborationServiceTest.php`
- `tests/Feature/Projects/ResourceAllocationServiceTest.php`
