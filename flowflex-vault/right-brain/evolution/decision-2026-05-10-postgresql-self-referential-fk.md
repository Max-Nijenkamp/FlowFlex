---
type: adr
date: 2026-05-10
status: decided
color: "#F97316"
---

# Decision: PostgreSQL Self-Referential FK Must Use Separate Schema::table Block

## Context

While building the `employees` table (migration 100001), the `manager_id` column references `employees.id` — a self-referential foreign key for the manager hierarchy. The initial implementation added `$table->foreign('manager_id')->references('id')->on('employees')` inside the `Schema::create()` closure.

PostgreSQL rejects this with:
```
there is no unique constraint matching given keys for referenced table "employees"
```

Laravel's Blueprint defers all FK constraints to `ALTER TABLE ... ADD CONSTRAINT` statements emitted after column definitions, but in PostgreSQL the primary key index has not yet been committed at the point these constraints are evaluated inside the same DDL transaction. MySQL accepts this because it processes table constraints differently.

The same issue appeared in the Projects domain `200001_create_projects_table.php` (`template_id` self-referencing `projects`).

## Options Considered

1. **Use `->primary()` explicitly** — tried; does not resolve the timing issue in PostgreSQL.
2. **Use `Schema::table()` after `Schema::create()`** — adds the FK as a separate `ALTER TABLE` statement, by which point the CREATE TABLE + PK index are fully committed. Works reliably.
3. **Use `DB::statement()` raw SQL** — verbose, bypasses Blueprint's portability layer. Not needed.

## Decision

All self-referential foreign keys MUST be declared in a separate `Schema::table()` block immediately after the `Schema::create()` block that creates the table.

Pattern:

```php
Schema::create('employees', function (Blueprint $table) {
    $table->ulid('id')->primary();
    // ... other columns ...
    $table->ulid('manager_id')->nullable();  // NO ->foreign() here
    // ... indexes ...
});

Schema::table('employees', function (Blueprint $table) {
    $table->foreign('manager_id')->references('id')->on('employees')->nullOnDelete();
});
```

The `down()` method must drop the FK before dropping the table:

```php
public function down(): void
{
    Schema::table('employees', function (Blueprint $table) {
        $table->dropForeign(['manager_id']);
    });
    Schema::dropIfExists('employees');
}
```

## Consequences

- Any future migration with a self-referential FK must follow this pattern
- Existing migrations using self-referential FKs in the create block must be fixed before running on PostgreSQL
- Pattern is slightly more verbose but fully portable (MySQL also handles it correctly)

## Related Left Brain

- [[employee-profiles]] — manager_id self-referential FK
- Builder log: [[builder-log-hr-phase2]]
- Gap: [[gap_postgresql-self-referential-fk]]
