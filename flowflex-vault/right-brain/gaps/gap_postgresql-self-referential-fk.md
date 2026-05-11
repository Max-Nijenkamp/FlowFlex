---
type: gap
severity: medium
category: architecture
status: resolved
color: "#F97316"
discovered: 2026-05-10
discovered_in: employee-profiles
last_updated: 2026-05-10
---

# Gap: PostgreSQL Self-Referential Foreign Key in Schema::create

## Context

Discovered while building the `employees` table migration (100001). The employees table has a `manager_id` column that references `employees.id` — a self-referential FK for the manager hierarchy.

## The Problem

PostgreSQL rejects a foreign key referencing the same table when defined inside a single `Schema::create()` block:

```
SQLSTATE[42830]: Invalid foreign key: 7 ERROR: there is no unique constraint
matching given keys for referenced table "employees"
```

The issue: Laravel emits all column definitions first, then emits `ALTER TABLE ... ADD CONSTRAINT` for FKs at the end of the CREATE TABLE block. In PostgreSQL, the primary key index has not been established at the point the FK constraint is added, so the FK constraint fails with "no unique constraint matching given keys".

The same bug was present in the Projects domain `200001_create_projects_table.php` migration (`template_id` self-referencing `projects.id`).

## Impact

- All HR phase 2 migrations fail (block the test suite via `RefreshDatabase`)
- Projects domain migrations also fail
- Tests cannot run at all

## Proposed Solution (applied)

Move the self-referential FK to a separate `Schema::table()` call AFTER the `Schema::create()` call. By the time the second statement runs, the `CREATE TABLE` has completed and the primary key constraint exists.

```php
// Inside Schema::create: declare column only, NO foreign()
$table->ulid('manager_id')->nullable();

// After Schema::create completes:
Schema::table('employees', function (Blueprint $table) {
    $table->foreign('manager_id')->references('id')->on('employees')->nullOnDelete();
});
```

## Status

Resolved — both `employees` and `projects` migrations fixed. Pattern documented in [[decision-2026-05-10-postgresql-self-referential-fk]].

## Links

- Builder log: [[builder-log-hr-phase2]]
- Related specs: [[employee-profiles]], [[payroll]]
- Decision: [[decision-2026-05-10-postgresql-self-referential-fk]]
