---
type: architecture
category: tenancy
pattern-key: tenancy
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Multi-Tenancy

Shared-database, shared-schema. All companies' data in one PostgreSQL database. Tenant isolation enforced at the application layer via global Eloquent scope.

**Why shared schema**: no per-tenant migrations, no connection pool explosion, simple super-admin cross-tenant analytics. Trade-off — a missing scope leaks data — mitigated by making the scope automatic via `BelongsToCompany`.

---

## CompanyContext Service

Singleton bound to the container. Holds the current company for one HTTP request or one queued job.

```php
class CompanyContext
{
    private ?Company $company = null;

    public function set(Company $company): void
    {
        $this->company = $company;
    }

    public function current(): Company
    {
        return $this->company ?? throw new MissingCompanyContextException();
    }

    public function currentId(): ?string
    {
        return $this->company?->id;
    }
}
```

`SetCompanyContext` middleware runs after authentication on every web request. It resolves the company from `$user->company_id`, calls `app(CompanyContext::class)->set($company)`, and calls `setPermissionsTeamId($company->id)`.

---

## BelongsToCompany Trait

```php
trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope());

        static::creating(function ($model) {
            if (! $model->company_id) {
                $model->company_id = app(CompanyContext::class)->current()->id;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
```

Three effects:
1. Registers `CompanyScope` — all queries filter by current company
2. Auto-sets `company_id` on create — prevents missing-scope bugs
3. Provides the `company()` relationship

---

## CompanyScope

```php
class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($companyId = app(CompanyContext::class)->currentId()) {
            $builder->where($model->getTable() . '.company_id', $companyId);
        }
    }
}
```

Invisible — builders never add `.where('company_id', ...)` manually.

**Bypassing** is only permitted in `/admin` panel (FlowFlex staff):
```php
// Admin panel only — never in app/Filament/App/, controllers, or services
Employee::withoutGlobalScope(CompanyScope::class)->find($id);
```

---

## Queue Context Restoration

HTTP requests set `CompanyContext` via middleware. Queue workers have no HTTP request — singleton is empty. The `WithCompanyContext` job middleware restores context:

```php
class WithCompanyContext
{
    public function handle(mixed $job, callable $next): void
    {
        $companyId = $job->event->company_id ?? $job->company_id ?? null;

        if ($companyId) {
            $company = Company::withoutGlobalScope(CompanyScope::class)->findOrFail($companyId);
            app(CompanyContext::class)->set($company);
            setPermissionsTeamId($company->id);
        }

        $next($job);
    }
}
```

All event listeners that touch tenant models must include this middleware. Events must carry `company_id` as a typed scalar, not a model reference.

---

## Spatie Permission Team Isolation

`setPermissionsTeamId($company->id)` must be called:
1. In `SetCompanyContext` middleware — every web request
2. In `WithCompanyContext` job middleware — every queued job touching permissions

Without this, `$user->hasRole('owner')` may return permissions from the wrong company's team.

---

## Tenant Isolation Checklist

Every new module migration and model must pass before merging:

- [ ] Migration: `company_id ulid not null references companies(id)` with index
- [ ] Model: `BelongsToCompany` trait
- [ ] Model: `HasUlids` trait
- [ ] Model: `SoftDeletes` trait
- [ ] No raw queries omitting `company_id` filter
- [ ] File uploads stored under `companies/{company_id}/...` via `FileStorageService::pathFor()` — never raw `Storage::put()`
- [ ] Events carry `company_id` as scalar in payload
- [ ] Queue jobs for these events use `WithCompanyContext` middleware
