---
type: adr
date: 2026-05-11
status: decided
color: "#F97316"
---

# ADR: User→Company Query Pattern in Select Dropdowns

## Context

Three Filament resources (FieldJobResource, PhysicalAssetResource, ExpenseResource) used `User::withoutGlobalScopes()->whereHas('companies', fn ($q) => $q->where('companies.id', ...))` to populate assigned-user Select dropdowns. This caused HTTP 500 on every create/edit page because `User` has no `companies()` relationship.

The `User` model uses a direct `company_id` column — users belong to exactly one company. There is no many-to-many pivot table between users and companies.

## Options

1. **Add a `companies()` belongsToMany to User** — over-engineering; unnecessary pivot table; would conflict with the single-company-per-user design.
2. **Use `where('company_id', $cid)` directly** — correct and matches the actual data model.
3. **Use BelongsToCompany trait scope** — only works when CompanyContext is set (which it is in Filament requests), but the trait's scope is applied via `booted()` not via a named relationship.

## Decision

**Option 2**: All Select dropdowns that load users filtered by company use:
```php
User::withoutGlobalScopes()
    ->where('company_id', app(CompanyContext::class)->currentId())
    ->pluck('email', 'id')
```

`withoutGlobalScopes()` is needed because the CompanyScope would otherwise be redundant but still applied. The explicit `where('company_id', ...)` is the canonical pattern.

## Consequences

- Any new resource with a user-picker must use this pattern, not `whereHas`.
- Search across files for `whereHas('companies')` is a red flag in this codebase.
- Related: BelongsToCompany child models in seeders must always receive explicit `company_id` — the trait only auto-sets via HTTP context.
