---
type: gap
severity: high
category: architecture
status: open
color: "#F97316"
discovered: 2026-05-09
discovered_in: admin-panel-flowflex
last_updated: 2026-05-09
---

# Gap: CompanyContext Singleton Leaks Across Horizon Worker Jobs

## Context

Discovered during Phase 0 audit. `CompanyContext` is registered as a singleton in `AppServiceProvider`. In HTTP requests this is correct — one context per request lifecycle. In Horizon queue workers, the same PHP process handles multiple jobs sequentially.

## The Problem

`CompanyContext` is a singleton. If a queued job sets the company context (`setPermissionsTeamId($company->id)` or `$context->set($company)`), the next job dispatched to the same Horizon worker process inherits that context. This means multi-tenant data operations in queue jobs will silently query the wrong company's data.

Phase 0 has no queue jobs yet, so this has not caused a bug. It will become a critical data-leak bug as soon as the first queued job uses `CompanyContext` or `setPermissionsTeamId`.

**File:** `app/Support/Services/CompanyContext.php`, `app/Providers/AppServiceProvider.php:14`

## Impact

- Cross-tenant data leak in queue workers
- Silent: no error thrown, wrong data returned
- Affects all future queued jobs that touch tenant-scoped models

## Proposed Solution

Every queued job that sets the company context must clear it in a `finally` block:

```php
public function handle(): void
{
    try {
        app(CompanyContext::class)->set($this->company);
        setPermissionsTeamId($this->company->id);
        // ... job logic
    } finally {
        app(CompanyContext::class)->clear();
        setPermissionsTeamId(null);
    }
}
```

Alternatively, create a `WithCompanyContext` job middleware that handles set/clear automatically. This is the preferred pattern and should be built before the first queue job is written.

## Links

- Source builder log: [[builder-log-admin-panel-flowflex]]
- Related: [[project-scaffolding]], [[multi-tenancy]]
