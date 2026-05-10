---
type: adr
date: 2026-05-10
status: decided
color: "#F97316"
---

# Decision: Always use `nullableUlidMorphs()` for morph relationships

## Context

During Phase 1 build, `database/migrations/2026_05_10_171102_create_activity_log_table.php` was initially created with `$table->nullableMorphs('subject')` and `$table->nullableMorphs('causer')`. Tests then failed with FK type mismatch: `subject_id` was a bigint but all model PKs in the codebase are ULIDs (strings).

## Options Considered

1. **`nullableMorphs()`** — Laravel default; creates `bigint` morph ID. Incompatible with ULID PKs.
2. **`nullableUlidMorphs()`** — Laravel built-in; creates `char(26)` morph ID. Matches ULID PKs everywhere.
3. **Custom migration column** — manually define `char(26) subject_id`. Equivalent to option 2 but without the helper.

## Decision

Use `nullableUlidMorphs('subject', 'subject')` and `nullableUlidMorphs('causer', 'causer')` in any migration that has a morph relationship, since all tables use ULID PKs.

The existing `activity_log` table had to be dropped and recreated in both `flowflex` and `flowflex_testing` databases directly via psql to fix the type mismatch.

## Consequences

- All future morph migrations must use `nullableUlidMorphs()` / `ulidMorphs()` — never the bigint variants
- Any third-party package migration that publishes with bigint morphs (e.g. spatie/laravel-activitylog) must be patched before running
- Test DB and prod DB must be kept in sync when fixing migration column types — use psql directly if needed

## Related Left Brain

- [[concept-multi-tenancy]] — ULID pattern is project-wide
- [[audit-log]] — first place this bit us
