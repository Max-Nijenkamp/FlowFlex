---
type: architecture
category: infra
pattern-key: search
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Search (Meilisearch)

Global and per-domain full-text search via Meilisearch 1.x + `laravel/scout`. Every searchable model is indexed per company. Search UI is a command palette (`Cmd+K` / `Ctrl+K`) in Filament panels and a per-resource search bar.

---

## Scout Configuration

```php
// config/scout.php
'driver' => 'meilisearch',
'prefix' => env('MEILISEARCH_INDEX_PREFIX', 'flowflex_'),

// config/meilisearch.php
'host' => env('MEILISEARCH_HOST', 'http://meilisearch:7700'),
'key' => env('MEILISEARCH_KEY'),
```

---

## Searchable Models

Models opt into search by implementing `Laravel\Scout\Searchable`:

```php
class Employee extends Model
{
    use HasUlids, BelongsToCompany, SoftDeletes, Searchable;

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,   // mandatory — used for filtering
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'job_title' => $this->job_title,
            'department' => $this->department?->name,
        ];
    }

    public function searchableAs(): string
    {
        return 'employees';
    }
}
```

**Critical**: `company_id` must be in `toSearchableArray()`. Every search query filters by `company_id` to prevent cross-tenant result leakage.

---

## Searchable Models Per Domain

| Domain | Models | Index Name |
|---|---|---|
| HR | Employee | `employees` |
| CRM | Contact, Account, Deal | `contacts`, `accounts`, `deals` |
| Finance | Invoice, Expense | `invoices`, `expenses` |
| Projects | Project, Task | `projects`, `tasks` |
| DMS | Document | `documents` |
| Support | Ticket | `tickets` |
| E-commerce | Product, Order | `products`, `orders` |

**Not indexed** (not needed): Audit log entries (too large), Payslips (sensitive, use DB filter instead), Journal entries.

---

## Tenant-Safe Search Query

Always filter by `company_id` in the Meilisearch query:

```php
// Correct — tenant-scoped search
$results = Employee::search($query)
    ->where('company_id', app(CompanyContext::class)->currentId())
    ->paginate(25);

// Wrong — returns results from ALL companies
$results = Employee::search($query)->paginate(25);
```

This is enforced by wrapping `Scout::search()` in a company-scoped helper:

```php
class TenantSearch
{
    public static function search(string $model, string $query): \Laravel\Scout\Builder
    {
        return $model::search($query)
            ->where('company_id', app(CompanyContext::class)->currentId());
    }
}
```

---

## Meilisearch Index Settings

Configured programmatically on index creation or via `artisan scout:sync-index-settings`:

```php
// For the employees index
'searchableAttributes' => ['first_name', 'last_name', 'email', 'job_title', 'department'],
'filterableAttributes' => ['company_id', 'status', 'department_id'],
'sortableAttributes' => ['last_name', 'hire_date', 'created_at'],
'typoTolerance' => ['enabled' => true, 'minWordSizeForTypos' => ['oneTypo' => 4, 'twoTypos' => 8]],
```

---

## Filament Global Search

Filament's built-in global search (`->globalSearch(true)` on resources) uses Eloquent search by default. Override with Scout:

```php
class EmployeeResource extends Resource
{
    protected static bool $globallySearchable = true;

    public static function getGlobalSearchResults(string $search): Collection
    {
        return TenantSearch::search(Employee::class, $search)
            ->take(5)
            ->get()
            ->map(fn (Employee $e) => [
                'title' => $e->full_name,
                'description' => $e->job_title,
                'url' => static::getUrl('view', ['record' => $e]),
            ]);
    }
}
```

---

## Indexing Strategy

**On write**: `Scout` automatically queues an index update via `ScoutModelObserver` when a model is saved or deleted.

**Bulk re-index**: run when adding new searchable attributes or after schema changes:

```bash
docker exec flowflex_app php artisan scout:import "App\Models\HR\Employee"
```

**Index prefix per environment**: prevents dev/staging data from polluting production indices when sharing a Meilisearch instance. Prefix: `flowflex_prod_`, `flowflex_staging_`, `flowflex_dev_`.

---

## Performance

Meilisearch search latency: typically <10ms for 100k documents. No application-level caching of search results — Meilisearch handles its own caching. Indexing is async (queued) so write latency is not affected.

Monitor index size via `php artisan scout:status`. If index grows beyond 5M documents, evaluate sharding by `company_id` prefix.
