---
type: adr
date: 2026-05-10
status: decided
color: "#F97316"
---

# Decision: Eloquent BelongsToMany pivot tables must use composite PK, never ULID id

## Context

During Phase 2 Projects build, the `sprint_tasks` pivot table was created with a `ulid('id')->primary()` column alongside `sprint_id` and `task_id` FK columns. When `Sprint::tasks()->syncWithoutDetaching([$taskId])` was called, PostgreSQL threw:

```
SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "id" of relation "sprint_tasks"
```

Root cause: Eloquent `BelongsToMany::sync()`, `attach()`, `syncWithoutDetaching()` insert **only the two FK columns** into pivot tables. A NOT NULL `id` column is never populated by Eloquent's pivot insert logic.

## Options Considered

1. **Override `newPivot()` on the model** to auto-generate a ULID on pivot creation — adds custom Pivot subclass to every pivot model; fragile and non-standard.
2. **Use composite PK `['sprint_id', 'task_id']`** — the standard Laravel pivot table pattern; Eloquent fully supports this; no custom logic needed.
3. **Use `$table->id()` autoincrement** — inconsistent with ULID-everywhere pattern; rejected.

## Decision

All Eloquent `BelongsToMany` pivot tables in FlowFlex must use **composite primary key** on the two FK columns:

```php
$table->primary(['sprint_id', 'task_id']);
```

No `id` column of any type on pivot tables. Pivot tables in FlowFlex: `project_members`, `sprint_tasks` (and any future pivot tables).

Exception: If a pivot table needs to be referenced by other tables as a FK target, use a separate migration to add a surrogate PK — but prefer redesigning as a first-class model instead.

## Consequences

- Simpler pivot migrations — no id column, no HasUlids on pivot models.
- Standard Laravel pivot behavior works out of the box.
- Pivot records cannot be referenced by FK from other tables — use a first-class model (e.g. `ProjectMember` → eligible for FK targets) if needed.
- All future pivot migrations must follow this pattern.

## Related Left Brain

- [[sprint-agile]] — sprint_tasks pivot
- [[task-management]] — project_members pivot
