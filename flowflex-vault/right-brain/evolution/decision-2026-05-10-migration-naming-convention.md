---
type: adr
date: 2026-05-10
status: decided
color: "#F97316"
---

# Decision: Domain migrations must use YYYY-MM-DD_NNNNNN_ prefix format

## Context

Phase 2 HR migrations were named `100001_create_employees_table.php` through `100010_*` and Projects migrations were named `200001_*` through `200011_*`. These numeric prefixes sort alphabetically BEFORE `2026_05_09_154712_create_permission_tables.php` (spatie/laravel-permission), which means when `RefreshDatabase` runs all migrations in sort order, the domain migrations executed before the `permissions` table was created. This caused `SQLSTATE[42P01]: relation "permissions" does not exist` errors in the full test suite whenever the `PermissionSeeder` (run in `beforeEach`) was reached after a migration set that ran before permissions.

## Options Considered

1. **Keep numeric prefix, fix by adding dependency checks in migrations** — fragile, migrations shouldn't have runtime logic checking for table existence.
2. **Rename to `YYYY-MM-DD_NNNNNN_*` date-prefixed format** — sort order is explicit; all domain migrations for a given day sort after the spatie permissions migration (`2026_05_09_*`).
3. **Use explicit migration batching / ordering in a config file** — Laravel doesn't support this natively without a package.

## Decision

Option 2: rename all domain migrations to `YYYY-MM-DD_NNNNNN_*` format where `YYYY-MM-DD` is the date the migration was created and `NNNNNN` is a 6-digit sequence number scoped to that domain.

- HR Phase 2: `2026_05_10_100001_*` through `2026_05_10_100010_*`
- Projects Phase 2: `2026_05_10_200001_*` through `2026_05_10_200011_*`

Convention going forward:
- Foundation (Phase 0): `000XXX_*` (legacy, works because all Foundation tables are independent of permissions)
- Phase 1 Core: `010XXX_*` (legacy, same reason)
- Phase 2+ domains: `YYYY-MM-DD_NNNNNN_*` (e.g. `2026_05_10_100001_*` for HR Phase 2)
- Each new domain gets its own 6-digit block: 100000s = HR, 200000s = Projects, 300000s = Finance, etc.

## Consequences

- All future Phase 2+ domain migrations must use the `YYYY-MM-DD_NNNNNN_*` format — ensures sort after `2026_05_09_*` permission tables.
- `migrate:fresh` and `RefreshDatabase` both respect file sort order — no runtime dependency issues.
- Domain blocks remain visually grouped by 6-digit prefix even within the date-prefixed format.
- Foundation (000XXX) and Phase 1 (010XXX) legacy naming kept as-is — they don't depend on permissions table.

## Related Left Brain

- No left-brain spec changes needed — this is a build-time convention only.
