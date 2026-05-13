---
type: module
domain: Foundation
panel: (scaffold — no panel)
module-key: foundation.tenancy
status: planned
color: "#4ADE80"
---

# Multi-Tenancy Layer

> Shared-database multi-tenancy via `BelongsToCompany` trait, `CompanyScope` global scope, and `CompanyContext` singleton — every tenant's data is invisible to every other tenant.

**Domain:** Foundation
**Module key:** `foundation.tenancy`

## What It Does

FlowFlex uses a shared PostgreSQL database with row-level isolation enforced by a global Eloquent scope. Every tenant model has a `company_id` column. The `BelongsToCompany` trait applies `CompanyScope` automatically, so all queries from that model are filtered to the current company without any controller-level code. `CompanyContext` is a request-scoped singleton that holds the resolved company for the lifecycle of each HTTP request. The `SetCompanyContext` middleware resolves the company from the authenticated user and boots the scope; it also blocks access for suspended companies.

## Features

### Core
- `BelongsToCompany` trait: declares `company_id` as a fillable column, boots `CompanyScope` via `addGlobalScope()`, adds `scopeForCompany()` helper, validates ownership before save
- `CompanyScope` global scope: adds `WHERE company_id = ?` to all Eloquent query builders; removed automatically when calling `withoutGlobalScopes()` for cross-company queries (billing service, admin panel)
- `CompanyContext` singleton: bound in `AppServiceProvider` as `app()->singleton(CompanyContext::class)` — exposes `set(Company $company)`, `get(): Company`, `clear()`, and `id(): string`
- `SetCompanyContext` middleware: resolves `company_id` from `auth()->user()`, loads the `Company`, calls `CompanyContext::set()`, calls `setPermissionsTeamId($company->id)` for Spatie Permission scoping, returns 403 for suspended companies

### Advanced
- Admin panel bypasses company context entirely — admin queries use `withoutGlobalScopes()` explicitly
- API routes use the same `SetCompanyContext` middleware after `auth:sanctum` resolves the API token's company
- `CompanyContext::set()` must be called before any Eloquent query on a tenant model — the middleware guarantees this for web and API requests
- Cross-company data access (e.g. billing calculations) must call `Company::withoutGlobalScopes()->findOrFail($id)` explicitly
- `spatie/laravel-permission` team scoping: `setPermissionsTeamId($company->id)` called in middleware so role lookups are always scoped to the current company

### AI-Powered
- Tenant isolation verification: automated test coverage asserts that data created by Company A is never visible when authenticated as Company B — run on every pull request

## Data Model

```erDiagram
    companies {
        ulid id PK
        string name
        string slug "unique"
        string status "active|trial|suspended|cancelled"
        string timezone
        string locale
        string currency
        timestamps created_at/updated_at
        timestamp deleted_at
    }

    users {
        ulid id PK
        ulid company_id FK
        string name
        string email
        string status "invited|active|inactive"
        timestamps created_at/updated_at
    }
```

| Layer | Class | Purpose |
|---|---|---|
| Trait | `App\Support\Traits\BelongsToCompany` | Boots scope, validates ownership |
| Scope | `App\Support\Scopes\CompanyScope` | WHERE company_id filter |
| Singleton | `App\Support\Services\CompanyContext` | Request-scoped company holder |
| Middleware | `App\Http\Middleware\SetCompanyContext` | Resolves and boots company per request |

## Permissions

- `foundation.tenancy.view`
- `foundation.tenancy.manage`
- `foundation.tenancy.bypass-scope`
- `foundation.tenancy.suspend-company`
- `foundation.tenancy.configure`

## Filament

- **Resource:** None (infrastructure — no UI)
- **Pages:** None
- **Custom pages:** None
- **Widgets:** None
- **Nav group:** N/A

## Related

- [[laravel-scaffold]]
- [[filament-panels]]
- [[test-suite]]
