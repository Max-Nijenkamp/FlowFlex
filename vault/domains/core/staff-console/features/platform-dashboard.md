---
domain: core
module: staff-console
feature: platform-dashboard
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Platform Dashboard

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

The `/admin` landing overview for FlowFlex staff: companies by status, revenue this month (paid invoices), open/past-due balance, an MRR estimate, and a 12-month revenue chart. Core Feature 5. Built from Filament widgets over billing + company data owned elsewhere — read-only.

## UI

- **Kind**: widget — `PlatformStatsWidget` (stats overview), `RevenueChartWidget` (chart), and `SystemHealthWidget` (over spatie/laravel-health) on the `/admin` dashboard.
- **Page**: the `/admin` panel dashboard (widgets, not a dedicated resource). Route: `/admin` dashboard.
- **Layout**: a stats-overview row (companies by status, revenue this month, open/past-due balance, MRR estimate), a 12-month revenue line/bar chart, and a system-health tile.
- **Key interactions**: passive read — staff land on `/admin` and see the summary; widgets refresh on their own poll. No mutation.
- **States**: empty (no companies/invoices yet → zeroed stats, empty chart) · loading (widget query/poll) · error (query failure → widget error state) · selected (n/a — read-only widgets).
- **Gating**: admin guard only — `canAccess() = auth('admin')->check()`. Cross-tenant aggregation is intentional and staff-only.

## Data

- Owns / writes: **no tables of its own, no writes** — read-only aggregation widgets.
- Reads: `billing_invoices` (revenue this month = paid invoices in current month; open/past-due balance) and `company_module_subscriptions` + user counts for the MRR estimate (MRR = Σ active-paid-module price × company user count), all cross-company read-only. Underlying tables **owned by [[../../billing-engine/_module]]** (invoices, subscriptions) and the foundation layer (`companies`, users). `SystemHealthWidget` reads spatie/laravel-health results (see [[../../health-monitoring/_module]]).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events) — figures are computed live from read-only queries against billing/company data.
- Feeds: none.
- Shared entity: `billing_invoices`, `company_module_subscriptions` (owned by [[../../billing-engine/_module]]); `companies`/users (foundation); health results (owned by [[../../health-monitoring/_module]]).

## Test Checklist

### Unit
- [ ] Revenue-this-month counts only paid invoices dated in the current month
- [ ] MRR estimate = Σ(active-paid-module price × company user count) across all companies

### Feature (Pest)
- [ ] Widgets aggregate cross-company (admin, `CompanyScope` no-ops); figures match seeded invoices/subscriptions
- [ ] Zero companies/invoices → zeroed stats and an empty chart (no error)

### Livewire
- [ ] Dashboard widgets deny render to a non-admin; admin sees the stats row, revenue chart, and health tile

## Related

- [[../_module]] · [[../architecture]] · [[../security]] · [[billing-overview]]
- [[../../billing-engine/_module]] · [[../../health-monitoring/_module]] · [[../../../../security/data-ownership]]
