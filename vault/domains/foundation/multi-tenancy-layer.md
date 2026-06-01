---
type: module
domain: Foundation
panel: (scaffold)
module-key: foundation.tenancy
status: planned
color: "#4ADE80"
---

# Multi-Tenancy Layer

Implements shared-database multi-tenancy: `CompanyContext` singleton, `CompanyScope` global scope, `BelongsToCompany` trait, `SetCompanyContext` middleware, and `WithCompanyContext` queue middleware. Every query on every tenant model automatically filters by the current company.

---

## Core Features

- `CompanyContext` singleton — holds current company for the duration of one HTTP request or queued job
- `CompanyScope` global scope — auto-applied to all Eloquent queries on models using `BelongsToCompany`
- `BelongsToCompany` trait — registers scope, auto-sets `company_id` on create, provides `company()` relation
- `SetCompanyContext` middleware — sets context from `$user->company_id` after authentication; calls `setPermissionsTeamId()`
- `WithCompanyContext` queue middleware — restores context in Horizon workers from `$job->company_id`
- `MissingCompanyContextException` — thrown when code calls `CompanyContext::current()` without middleware having run
- All events dispatched from domain services carry `company_id` as a typed scalar property

---

## Data Model

No additional tables — uses `companies` and `users` from [[domains/foundation/laravel-scaffold]].

---

## Filament

No Filament resources — infrastructure only. See [[architecture/multi-tenancy]] for full implementation.

---

## Related

- [[architecture/multi-tenancy]]
- [[architecture/patterns/belongs-to-company]]
- [[domains/foundation/filament-panels]]
