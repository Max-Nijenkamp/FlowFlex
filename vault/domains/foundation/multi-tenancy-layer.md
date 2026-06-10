---
type: module
domain: Foundation
domain-key: foundation
panel: (scaffold)
module-key: foundation.tenancy
status: planned
priority: v1-core
depends-on: [foundation.scaffold]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [tenancy, model]
tables: []
permission-prefix: ""
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Multi-Tenancy Layer

Implements shared-database multi-tenancy: `CompanyContext` singleton, `CompanyScope` global scope, `BelongsToCompany` trait, `SetCompanyContext` middleware, and `WithCompanyContext` queue middleware. Every query on every tenant model automatically filters by the current company. **The single most security-critical module in the codebase.**

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/laravel-scaffold\|foundation.scaffold]] | `companies`/`users` tables + spatie/permission installed |

---

## Core Features

- `CompanyContext` singleton — holds current company for the duration of one HTTP request or queued job
- `CompanyScope` global scope — auto-applied to all Eloquent queries on models using `BelongsToCompany`
- `BelongsToCompany` trait — registers scope, auto-sets `company_id` on create, provides `company()` relation
- `SetCompanyContext` middleware — sets context from `$user->company_id` after authentication; calls `setPermissionsTeamId()`
- `WithCompanyContext` queue middleware — restores context in Horizon workers from `$job->company_id` / `$job->event->company_id`
- `MissingCompanyContextException` — thrown when code calls `CompanyContext::current()` without middleware having run
- All events dispatched from domain services carry `company_id` as a typed scalar property

Full implementation code: [[architecture/multi-tenancy]] (this spec adds nothing to it — build exactly that).

---

## Data Model

No additional tables — uses `companies` and `users` from [[domains/foundation/laravel-scaffold]].

## DTOs

None.

## Services & Actions

- `CompanyContext::set(Company $company): void`
- `CompanyContext::current(): Company` — throws `MissingCompanyContextException`
- `CompanyContext::currentId(): ?string`

## Filament / Permissions

None — infrastructure. Middleware is wired into panels by [[domains/foundation/filament-panels]].

---

## Test Checklist

- [ ] Tenant isolation: model query under company A context returns zero company B rows
- [ ] `creating` hook auto-fills `company_id` from context
- [ ] `CompanyContext::current()` without context throws `MissingCompanyContextException`
- [ ] `WithCompanyContext` restores context + `setPermissionsTeamId` in a queued job
- [ ] Queued job WITHOUT `company_id` and with the middleware: passes through without setting context (no crash)
- [ ] `withoutGlobalScope(CompanyScope::class)` works (admin path) and is covered by an arch test forbidding it outside `app/Filament/Admin` + `app/Support`
- [ ] Permission check under company A team does not leak company B roles

---

## Build Manifest

```
app/Support/Services/CompanyContext.php
app/Support/Scopes/CompanyScope.php
app/Support/Traits/BelongsToCompany.php
app/Http/Middleware/SetCompanyContext.php
app/Support/Jobs/Middleware/WithCompanyContext.php
app/Exceptions/MissingCompanyContextException.php
tests/Feature/Foundation/TenantIsolationTest.php
tests/Architecture/TenancyTest.php
```

---

## Related

- [[architecture/multi-tenancy]]
- [[architecture/patterns/belongs-to-company]]
- [[domains/foundation/filament-panels]]
- [[domains/foundation/queue-workers]]
