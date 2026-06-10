---
type: architecture
category: quality
pattern-key: performance
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Performance Patterns

Performance constraints, anti-patterns, and techniques for keeping FlowFlex fast at scale. The goal: every page load under 200ms p95, every API response under 100ms p95.

---

## N+1 Query Prevention

**The rule**: every relationship accessed in a loop must be eager-loaded. No exceptions.

```php
// Wrong — N+1: 1 query for employees + N queries for department name
$employees = Employee::all();
foreach ($employees as $emp) {
    echo $emp->department->name; // query per employee
}

// Correct — 2 queries total
$employees = Employee::with('department')->paginate(25);
```

**Filament resources**: use `getEloquentQuery()` to add eager loading:

```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->with(['department', 'manager:id,first_name,last_name']);
}
```

**Detection**: Laravel Telescope shows N+1 queries in development. A test query count assertion catches regressions:

```php
it('lists employees without N+1', function () {
    Employee::factory()->count(10)->for($company)->create();

    $queryCount = 0;
    DB::listen(fn () => $queryCount++);

    actingAs($user)->get('/hr/employees');

    expect($queryCount)->toBeLessThan(5); // list + 1-2 joins, never 10+
});
```

---

## Pagination

All list endpoints and Filament tables are paginated. Never load an unbounded result set.

```php
// API: always paginate
return $this->employees->list($data); // returns LengthAwarePaginator

// Filament: default page size 25, allow 10/25/50
public static function table(Table $table): Table
{
    return $table
        ->paginated([10, 25, 50])
        ->defaultPaginationPageOption(25);
}
```

Export operations (Excel, CSV) process in a queued job using chunking:

```php
Employee::chunk(500, function ($employees) use ($sheet) {
    foreach ($employees as $emp) {
        $sheet->appendRow($emp->toArray());
    }
});
```

---

## Database Query Optimization

**Index every foreign key**: `company_id` is always indexed (enforced by migration checklist). Add compound indexes for common filter combinations:

```php
// Common: filter employees by company + status
$table->index(['company_id', 'status']);

// Common: filter invoices by company + due_date range
$table->index(['company_id', 'due_date']);

// Common: search activities by company + contact
$table->index(['company_id', 'contact_id']);
```

**Select only needed columns** for list views:

```php
Employee::select(['id', 'first_name', 'last_name', 'email', 'status', 'department_id'])
    ->with('department:id,name')
    ->paginate(25);
```

**Avoid `COUNT(*)` on large tables** — use Redis-cached counters for frequently needed totals (headcount, open ticket count, pipeline deal count).

**Use `whereHas()` sparingly** — it generates a correlated subquery. Prefer a direct join:

```php
// Slow on large tables
Employee::whereHas('department', fn ($q) => $q->where('name', 'Engineering'))->get();

// Fast — uses the index
Employee::join('hr_departments', 'hr_departments.id', '=', 'hr_employees.department_id')
    ->where('hr_departments.name', 'Engineering')
    ->select('hr_employees.*')
    ->paginate(25);
```

---

## Filament Resource Performance

**Defer non-critical stats**: `StatsOverview` widgets that aggregate data should use `lazy()` loading — they render after the page loads:

```php
class HrStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = true; // renders after initial page load
}
```

**Table column optimization**: avoid computed columns that load relations per row. Use `getStateUsing()` only for pre-loaded data:

```php
// Wrong — loads department relationship per row (N+1)
TextColumn::make('department.name'),

// Correct — use getEloquentQuery() to eager load
TextColumn::make('department_name')
    ->getStateUsing(fn ($record) => $record->department?->name),
// AND in getEloquentQuery(): ->with('department:id,name')
```

---

## Caching Query Results

See [[architecture/caching]] for full caching strategy. Quick reference:

- Module subscriptions: 5 min TTL
- Dashboard widget counts: 15 min TTL
- Heavy financial reports: 1 hr TTL (historical periods only)
- Spatie permissions: 1 hr TTL

---

## Meilisearch vs Database Search

Use Meilisearch for:
- Global command palette search across multiple models
- Full-text search with typo tolerance
- Any search field with 3+ words expected

Use database `LIKE`/`ILIKE` for:
- Simple single-column lookups (email exact match, invoice number)
- Admin-only queries that don't need relevance ranking
- Searches on fields not worth maintaining a Meilisearch index for

See [[architecture/search]] for full search patterns.

---

## HTTP Response Optimization

**Inertia partial reloads**: use Inertia's `only()` to reload only the data that changed, not the full page:

```javascript
router.reload({ only: ['employees'] })
```

**Filament Livewire polling**: avoid `$wire.poll()` on large data sets. Use Reverb WebSocket events instead — see [[architecture/websockets]].

**Optimize images**: all uploaded images processed by Spatie Media Library with responsive image generation and WebP conversion:

```php
// In model
public function registerMediaConversions(?Media $media = null): void
{
    $this->addMediaConversion('thumb')
        ->width(100)->height(100)->format('webp');

    $this->addMediaConversion('preview')
        ->width(800)->format('webp');
}
```

---

## Performance Monitoring

**Laravel Telescope** (dev only): tracks slow queries (>100ms), N+1 queries, memory usage, job execution time.

**Laravel Pulse** (all environments): tracks:
- Slow outgoing requests
- Slow queries
- Exceptions
- Queue throughput and depth
- Cache hit/miss ratio

**Pulse dashboard** at `/pulse` (admin panel, authenticated). Key alerts:
- Query time >200ms → investigate + add index
- Cache hit rate <80% → review TTLs
- Queue depth >100 on `domain-events` → scale workers
