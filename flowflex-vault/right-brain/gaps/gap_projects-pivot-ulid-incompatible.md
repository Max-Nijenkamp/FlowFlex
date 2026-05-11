---
type: gap
tag: gap/architecture
severity: medium
category: architecture
status: resolved
color: "#F97316"
discovered: 2026-05-10
discovered_in: sprint-agile
last_updated: 2026-05-10
---

# Gap: BelongsToMany pivot table ULID id incompatible with Eloquent insert

## Context

During Projects Phase 2 build, the `sprint_tasks` pivot table was scaffolded with a `ulid('id')->primary()` column following the FlowFlex ULID-everywhere convention. This caused a NOT NULL violation when attaching tasks to sprints via `BelongsToMany`.

## The Problem

Eloquent's `BelongsToMany` (attach, sync, syncWithoutDetaching) inserts **only the two FK columns** into pivot tables. It has no mechanism to auto-populate a ULID `id` column. The `HasUlids` trait only fires on `Model::creating` — pivot inserts bypass the Eloquent model lifecycle entirely and use a raw `DB::table()->insert()` internally.

Error hit:
```
SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "id"
of relation "sprint_tasks" violates not-null constraint
```

## Impact

Any pivot table with a `ulid('id')` or other NOT NULL primary key column will fail when used with standard Eloquent `BelongsToMany` methods. This includes `attach()`, `detach()`, `sync()`, `syncWithoutDetaching()`, `toggle()`.

## Proposed Solution

**Resolved:** Remove `id` column from pivot tables. Use composite PK on the two FK columns:

```php
$table->primary(['sprint_id', 'task_id']);
```

ADR logged: [[decision-2026-05-10-pivot-composite-pk]]

## Resolution

Fixed in `200009_create_sprint_tasks_table.php` — rolled back migration, removed `ulid('id')->primary()`, replaced with `$table->primary(['sprint_id', 'task_id'])`, re-ran migrate.

Pattern applied retroactively to verify `project_members` (200002) — it correctly uses composite PK already.

## Links

- Builder log: [[builder-log-projects-phase2]]
- ADR: [[decision-2026-05-10-pivot-composite-pk]]
- Related spec: [[sprint-agile]]
