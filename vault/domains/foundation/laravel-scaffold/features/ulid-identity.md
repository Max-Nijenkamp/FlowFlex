---
domain: foundation
module: laravel-scaffold
feature: ulid-identity
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# ULID Identity Strategy

Every model's primary key is a ULID (`HasUlids`), not an auto-increment integer — the identity convention the whole app inherits.

## Behaviour

- All models use the `HasUlids` trait → 26-char lexicographically-sortable string PKs.
- Foreign keys use `foreignUlid()` in migrations; `company_id` is a `foreignUlid` on every tenant table.
- ULIDs are time-ordered (sortable by creation) yet non-sequential → no tenant-count/row-count leakage.
- Enforced by arch test: `ModelsTest` asserts models declare `HasUlids` ([[../test-suite/_module|test-suite]]).

## UI

- **Kind**: background (identity convention — no screen). IDs surface only inside other modules' resources
  (usually hidden columns / route params).

## Data

- Owns: no tables (a convention applied across all tables). Reads/writes: none of its own.
- Cross-domain writes: none.

## Relations

- Consumes: nothing. Feeds: every module's models depend on this PK shape.
- Shared entity: the `HasUlids` convention itself.

## Test Checklist

### Unit
- [x] A new model's PK is a 26-char ULID, lexicographically sortable by creation

### Feature (Pest)
- [x] `ModelsTest` asserts models declare `HasUlids` (arch gate)
- [x] Tenant table migrations use `foreignUlid('company_id')`

## Unknowns

> [!warning] UNVERIFIED — whether `strict_types=1` and a "ULID PK on every model" rule are both enforced by a
> single arch test or split. `ModelsTest` presence is confirmed; exact assertions not re-read.

## Related

- [[../_module|Laravel Scaffold]] · [[../data-model]] · [[../../../../architecture/patterns/belongs-to-company]]
