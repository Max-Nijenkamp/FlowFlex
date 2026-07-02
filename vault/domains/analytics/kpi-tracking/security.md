---
domain: analytics
module: kpi-tracking
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# KPI Tracking — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `analytics.kpis.view-any` | View KPIs + KPI dashboard |
| `analytics.kpis.manage` | Create / edit / delete KPI definitions |
| `analytics.kpis.record-values` | Enter manual actuals for a manual-source KPI |

Seeded in `PermissionSeeder`. **Verb-per-command:** `record-values` (the manual-value command) is distinct from `manage` (definition CRUD). KPI status bands and threshold breaches are computed/automated, not user commands, so they need no permission of their own; the alert dispatch is authorised by the scheduler, not a user.

---

## Rate Limiting

No user-triggered file-generating, comms, money, or external-API action originates in this module, so no `panel-action`/`exports` limiter is attached here. The one outbound side effect — the below-threshold alert — is dispatched through `core.notifications`, whose own throttling governs delivery, and is additionally bounded by the `bi_kpi_snapshots.alerted` once-per-period guard (never more than one alert per KPI per period). Manual value entry is an ordinary scoped write. *(assumed — no rate-limited surface in v1)*

---

## Access Contract

```php
canAccess() = Auth::user()->can('analytics.kpis.view-any')
           && BillingService::hasModule('analytics.kpis')
```

Per [[../../../architecture/filament-patterns]] #1 — the custom `KpiDashboardPage` states `canAccess()` explicitly.

---

## Key controls

1. **Metric source must be registered + active.** A metric-sourced KPI can only reference a key that exists in `MetricRegistry` **and** whose module is active — validated on `CreateKpiData`. No free-form data path.
2. **Alerts via the notifications service.** Below-threshold notifications are dispatched through `core.notifications`; Analytics never writes notification tables ([[../../../security/data-ownership]]).
3. **Alert once-guard.** `bi_kpi_snapshots.alerted` prevents duplicate alerts per period.

---

## Tenant Isolation

- `bi_kpis` + `bi_kpi_snapshots` scoped by `company_id` via `BelongsToCompany` + `CompanyScope`.
- Snapshot capture runs per company under `CompanyContext`; metric resolution runs under the owning domain's context.

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
