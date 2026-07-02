---
domain: analytics
module: report-builder
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Report Builder — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `analytics.reports.view-any` | View / open reports + the builder |
| `analytics.reports.create` | Create / edit report definitions |
| `analytics.reports.update` | Edit an existing saved report definition |
| `analytics.reports.delete` | Delete a saved report *(assumed)* |
| `analytics.reports.run` | Run a report (preview) |
| `analytics.reports.export` | Export a report to Excel/CSV |

Seeded in `PermissionSeeder`. **Verb-per-command:** `run` and `export` are distinct commands (each is a builder button + a `ReportResource` row action), separate from the CRUD verbs on the definition.

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
4. **Rate limiting** — see `## Rate Limiting` below; exports are chunked.

---

## Rate Limiting

| Action | Limiter | Notes |
|---|---|---|
| Export report (`export` / `ExportReportJob`) | **`exports`** (5/hr per company) | File-generating action — MUST cite the `exports` limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]; job runs on the `exports` queue, chunked |
| Run / preview (`run`) | `panel-action` | Panel action that composes a potentially heavy CompanyScope query (calls source domains) — default `panel-action` limiter *(assumed)* |

Both limiters keyed per company. Preview is additionally capped at 100 rows ([[../../../architecture/security]]).

---

## Tenant Isolation

- `bi_reports` scoped by `company_id` via `BelongsToCompany` + `CompanyScope`.
- Every run/export executes under `CompanyContext` — including the queued `ExportReportJob` (`WithCompanyContext`).

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
