---
domain: customer-success
module: success-analytics
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Success Analytics — Architecture

## Services & Actions

Interface→Service: `CsAnalyticsServiceInterface` → `CsAnalyticsService`.

- `metrics(CarbonInterface $from, CarbonInterface $to): CsMetricsData` — assembles all sections. Soft sections are computed only when their source module is active (NRR needs `finance.invoicing`; NPS trend needs `cs.nps`; effectiveness needs `cs.playbooks`; at-risk needs `cs.churn`). Uses `brick/money` for NRR arithmetic. Read paths are N+1-safe.

No tables, no writes. Every input is a read-only query through another module's service/read API.

Data-ownership note: this is the reference module for [[../../../security/data-ownership]] — it demonstrates a domain that reads broadly and writes nothing.

---

## Events

### Fires / Consumes
None. Read-only module; metrics are pulled on view.

---

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:cs:metrics:{from}:{to}` | 1 h historical / 15 min current window | TTL only |

Heavy aggregations are cached per [[../../../architecture/caching]] to keep the dashboard responsive.

---

## Filament Artifacts

**Nav group:** Customer Success

| Artifact | Kind ([[../../../architecture/patterns/feature-ui-spec]]) | Notes |
|---|---|---|
| `CsDashboardPage` | custom-page (+ apex charts) | date-range filter; retention/churn, NRR, health distribution, NPS trend, at-risk, CSM performance, playbook effectiveness; **Export** |
| `RetentionWidget` / `NrrWidget` / `HealthDistributionWidget` / `CsmPerformanceWidget` | widget | reusable fragments composed on the dashboard |

**Access contract:** `canAccess() = Auth::user()->can('cs.analytics.view') && BillingService::hasModule('cs.analytics')` per [[../../../architecture/filament-patterns]] #1 — the custom page states it explicitly. Soft sections are hidden when their source module is inactive. No public/portal surface.

---

## Search & Realtime

- Search: none.
- Realtime: none — dashboard is cached aggregates with a date filter.

---

## Security Notes

- **Export rate limiter (medium)** — the export action carries a per-user throttle to prevent heavy repeated aggregation exports ([[./security]]).
- No encrypted fields; no public endpoints.

See [[./security]] for the full access contract and permissions.
