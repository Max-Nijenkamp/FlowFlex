---
type: gap
severity: critical
category: architecture
status: resolved
color: "#F97316"
discovered: 2026-05-11
discovered_in: api-integrations-layer
last_updated: 2026-05-11
---

# Gap: API Routes Missing SetCompanyContext Middleware (CRITICAL-3)

## Context

`SetCompanyContext` middleware was appended to the `web` middleware group in `bootstrap/app.php`. The `api` middleware group did not include it. Any authenticated API request had no company context → `CompanyContext::currentId()` returned null.

## The Problem

- `ProjectController`, `TaskController`, `EmployeeController` all call `$this->companyContext->currentId()` to scope queries and set `company_id` on new records
- Without context, all queries returned unscoped data (all companies) or failed with NOT NULL violation on `company_id`
- `CompanyScope` is fail-open (no filter when context absent) → API users could see ALL companies' data

## Impact

Critical data leak + data integrity failure for all API endpoints.

## Resolution

Added `SetCompanyContext::class` to the authenticated API route group in `routes/api.php`:

```php
Route::middleware(['auth:sanctum', SetCompanyContext::class])->group(function (): void {
    // protected routes
});
```

`SetCompanyContext` uses `$request->user()` to get the authenticated user, then loads `$user->company` and sets context. Works identically via Sanctum tokens as via web sessions.
