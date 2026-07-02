---
domain: analytics
module: report-builder
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Report Builder — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `analytics.reports.view-any` | View / open reports + the builder |
| `analytics.reports.create` | Create / edit report definitions |
| `analytics.reports.run` | Run a report (preview) |
| `analytics.reports.export` | Export a report to Excel/CSV |

---

## Access Contract

```php
canAccess() = Auth::user()->can('analytics.reports.view-any')
           && BillingService::hasModule('analytics.reports')
```

Per [[../../../architecture/filament-patterns]] #1 — the custom `ReportBuilderPage` states `canAccess()` explicitly.

---

## The domain-defining controls

1. **Column whitelist.** Only columns a domain registers as `whitelisted_columns` are selectable/filterable/groupable. **Encrypted and sensitive fields are never whitelisted** — they cannot be reported, previewed, or exported. Enforced on `CreateReportData` *and* at run time.
2. **Eloquent-only, CompanyScope-bound.** The runner composes Eloquent queries under `CompanyScope` — never raw SQL. A report can never return another company's rows; **`ReportIsolationTest` is the domain's most important test.**
3. **Module gating.** Inactive-module sources are hidden; a saved report on a now-inactive source cannot run.
4. **Rate limiting** (medium, per [[../../../build/security-audit-2026-06-11]]): run + export actions are throttled ([[../../../architecture/security]]); exports are chunked.

---

## Tenant Isolation

- `bi_reports` scoped by `company_id` via `BelongsToCompany` + `CompanyScope`.
- Every run/export executes under `CompanyContext` — including the queued `ExportReportJob` (`WithCompanyContext`).

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
