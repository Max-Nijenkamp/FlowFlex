---
tags: [flowflex, performance, rules, phase/1]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Performance Rules

Non-negotiable performance rules. Every module, every query, every background job must follow these.

## The Rules

### 1. Never Load N+1 Queries

Always eager-load relationships. Use `with()` and Filament's `->with()` on tables.

```php
// WRONG — N+1 on every row
Employee::all()->each(fn($e) => $e->department->name);

// CORRECT — one query
Employee::with('department')->get();

// In Filament table:
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->with(['department', 'manager']);
}
```

Use Laravel Telescope (local) to spot N+1 queries during development. Any query count above expected is a bug.

### 2. Queue All Slow Jobs

PDF generation, payslip creation, report building, email sends, API sync jobs — all queued. **Never block the HTTP request.**

Jobs that must always be queued:
- PDF generation (payslips, invoices, reports)
- Email sends (transactional, bulk)
- Payroll run processing
- Report building
- File uploads to S3 (large files)
- External API sync (Stripe, Xero, QuickBooks, Shopify)
- Webhook dispatches
- OCR processing
- Notification dispatch (bulk)

```php
// WRONG — blocks the HTTP response for seconds
GeneratePayslipPDF::dispatch($payRun)->dispatchSync();

// CORRECT — returns immediately, processes in background
GeneratePayslipPDF::dispatch($payRun);
```

Queue priority:
- `high` — user-facing real-time actions (notifications, session events)
- `default` — standard jobs
- `low` — reports, bulk exports, non-urgent sync

### 3. Cache Expensive Reads

Module registry, permission checks, dashboard aggregates — use Redis with explicit cache keys per tenant. Bust on change.

```php
// Cache pattern: {type}.{tenant_id}[.{additional_key}]
$modules = Cache::remember(
    "module_registry.{$tenant->id}",
    3600,
    fn() => $tenant->activeModules()->pluck('module_key')->all()
);

// Bust on change:
Cache::forget("module_registry.{$tenant->id}"); // on ModuleActivated / ModuleDeactivated
```

Must-cache items:
- Module registry per tenant (active modules list)
- Permission sets per user (resolved permission list)
- Dashboard aggregate metrics (recalculate on schedule, not on every page load)
- Currency exchange rates (refresh hourly)
- Public holiday calendars (refresh daily)

### 4. Paginate All Table Views

Default 25 rows. No `->get()` on large tables.

```php
// WRONG — loads all records
Employee::all();

// CORRECT — paginated
Employee::paginate(25);

// In Filament table — Filament paginates automatically
// Default page size: 25
// Never change to 'all' or very large numbers
```

### 5. Use Database Indexes

Every `tenant_id`, every status column, every foreign key. Check query plans in development.

```php
// In migration — every module table needs these at minimum:
$table->index('tenant_id');
$table->index('status');
$table->index('created_at');
$table->index(['tenant_id', 'status']); // composite for common filter combinations
```

Foreign key indexes are automatically created by Laravel with `->foreign()` but double-check on composite lookups.

Use `EXPLAIN ANALYZE` in PostgreSQL during development to verify query plans on large datasets.

## Monitoring

- **Laravel Horizon** — monitors queue health, throughput, wait times
- **Laravel Telescope** — local development profiling (queries, jobs, events, notifications)
- Both are required during development; Horizon runs in production

## Related

- [[Architecture]]
- [[Tech Stack]]
- [[Security Rules]]
- [[Naming Conventions]]
