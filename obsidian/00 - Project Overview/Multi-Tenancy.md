---
tags: [flowflex, multi-tenancy, spatie, tenant, phase/1]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Multi-Tenancy

Every module, every query, every piece of data in FlowFlex is tenant-scoped. No data leaks between workspaces — ever.

## Core Rules

- Every database table that belongs to a module has a `tenant_id` column
- Every Eloquent query is automatically scoped to the current tenant via a global scope
- Tenants are fully isolated — no data leaks between workspaces
- Each tenant has: custom subdomain, optional custom domain (CNAME), branding config, locale, timezone
- Use `spatie/laravel-multitenancy` for tenant resolution and context switching

## Package

**spatie/laravel-multitenancy** — handles:
- Tenant model resolution from the current request (subdomain or custom domain)
- Tenant context switching (sets the current tenant for the request lifecycle)
- Database connection switching (if using separate schemas per tenant)
- Tenant-aware queue jobs

## Tenant Identification

Tenants are identified by:
1. **Subdomain** — `acmecorp.flowflex.com` resolves to tenant `acmecorp`
2. **Custom domain** — `app.acmecorp.com` (CNAME pointing to FlowFlex) resolved via lookup table

## BelongsToTenant Trait

Every module model must apply the `BelongsToTenant` trait:

```php
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Employee extends Model
{
    use BelongsToTenant;
    // tenant_id is automatically set on create
    // global scope automatically filters all queries
}
```

This ensures:
- `tenant_id` is auto-set on `creating`
- A global scope applies `WHERE tenant_id = ?` to all queries
- Never possible to accidentally query another tenant's data

## Tenant Data Structure

```sql
-- Every module table has these columns at minimum:
id          ULID PRIMARY KEY
tenant_id   ULID NOT NULL REFERENCES tenants(id)
created_at  TIMESTAMP
updated_at  TIMESTAMP
deleted_at  TIMESTAMP NULL  -- for soft-deletes
```

## Workspace Configuration Per Tenant

Each tenant workspace has:
- **Custom subdomain** (`companyname.flowflex.com`)
- **Optional custom domain** (CNAME record, DNS-verified)
- **Branding config** — logo (light + dark), primary brand colour, email sender name
- **Locale** — language, date format, number format, first day of week
- **Timezone** — all timestamps displayed in workspace timezone
- **Primary currency** — for invoices, budgets, financial reports
- **Active modules** — which modules the tenant has toggled on
- **Plan tier** — Starter / Pro / Enterprise

## Module Registry

A `modules` table (or `tenant_modules` pivot) tracks which modules each tenant has active:

```sql
CREATE TABLE tenant_modules (
  id          ULID PRIMARY KEY,
  tenant_id   ULID NOT NULL,
  module_key  VARCHAR NOT NULL,  -- e.g. 'hr', 'finance', 'payroll'
  is_active   BOOLEAN DEFAULT TRUE,
  activated_at TIMESTAMP,
  deactivated_at TIMESTAMP NULL,
  -- ...
);
```

When a tenant deactivates a module:
- Filament panel resources for that module are hidden
- Data is **retained** — never deleted on deactivation
- If reactivated, all historical data is immediately available again

## Module Registry Caching

The module registry (is module X active for tenant Y?) is expensive to query on every request. It **must be cached**:

```php
// Cache key pattern: module_registry.{tenant_id}
// Bust on: ModuleActivated, ModuleDeactivated events
Cache::remember("module_registry.{$tenant->id}", 3600, function () use ($tenant) {
    return $tenant->activeModules()->pluck('module_key')->all();
});
```

## Backup & Data Management

- Scheduled workspace data exports (CSV + JSON per module)
- On-demand full export ("Download everything")
- Data retention policies per module (auto-delete old records after N years)
- Workspace deletion with 30-day recovery window
- GDPR data erasure request (deletes all PII across all modules)

## Security Note

**Never bypass the global tenant scope.** There is no legitimate reason to do so in application code. If you need cross-tenant queries (FlowFlex super-admin use case only), use explicit `withoutGlobalScope()` in a dedicated admin-only service — never in module code.

## Related

- [[Architecture]]
- [[Tech Stack]]
- [[Multi-Tenancy & Workspace]]
- [[Security Rules]]
- [[Module Billing Engine]]
