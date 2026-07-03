---
domain: it
module: it-reporting
feature: it-dashboard
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: IT Dashboard

## Purpose

The single reporting surface for the IT domain — hosts every IT reporting widget on one apex-chart dashboard with a header period filter and a throttled export action.

## Behavior

- Custom Filament page (`ItDashboardPage`) in the **Reporting** nav group, built on apex charts ([[../../../../architecture/patterns/custom-pages]]).
- Header period filter (`from`/`to`) re-scopes every widget; results come from `ItAnalyticsService::metrics(from, to)` behind the TTL cache.
- Soft-dep sections render **conditionally**: the licence, helpdesk, compliance and access widgets appear only when their module is active (`BillingService::hasModule(...)`); an inactive module simply omits its widget — no error, no empty card.
- Export action produces the current report (throttled per company-user).

## UI

- **Kind**: custom-page — dashboard of apex-chart widgets ([[../../../../architecture/patterns/custom-pages]], ui-strategy #6).
- **Page**: `ItDashboardPage` at `/it/reporting` (custom Filament page + apex-chart widgets).
- **Layout**: header period filter; grid of widgets — AssetValueWidget (always), then LicenceSpendWidget / HelpdeskWidget / ComplianceWidget rendered conditionally on their soft-dep module; upcoming renewals + warranty expiries list.
- **Key interactions**: change the header period → all widgets re-scope; hover a series for tooltip; **export** the current report from a header action (named-throttled).
- **States**: empty (no assets yet → placeholder in AssetValueWidget; soft-dep off → its widget absent) · loading (skeleton charts while aggregate/cache resolves) · error ("Couldn't load metrics" card with retry) · selected (hovered/focused data point highlighted).
- **Gating**: visible with `it.reporting.view` and `it.reporting` module active; export requires `it.reporting.view` (throttled action).

## Data

- **Owns / writes: NOTHING** — this page owns no tables and performs no writes of any kind.
- Reads (read-only, via each owning module's read API): `it_assets` (it.assets), `it_licences` (it.licences), `it_tickets` (it.helpdesk), `it_mdm_devices` (it.mdm), `it_access_grants` (it.access) — all aggregated in `ItAnalyticsService::metrics` → `ItMetricsData`.
- **Cross-domain writes: none at all** — dashboards only; never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Reads from `it.assets` / `it.licences` / `it.helpdesk` / `it.mdm` / `it.access` (all read-only).
- Consumes: nothing (recomputes live per request, TTL-cached).
- Feeds: nothing (read-only dashboard, emits no events).

## Test Checklist

### Unit
- [ ] Cache key embeds company_id + from/to window; historical range TTL 1h vs current-period 15min

### Feature (Pest)
- [ ] Soft-dep widgets (licence/helpdesk/compliance) render only when their module is active; inactive module omits its widget with no error
- [ ] Export action is throttled per company-user (over-limit -> 429) and requires `it.reporting.view`
- [ ] Tenant isolation: metrics for company A never leak into company B's cached response

### Livewire
- [ ] `ItDashboardPage` canAccess(): hidden without `it.reporting.view` or with `it.reporting` inactive
- [ ] Header period filter re-scopes widgets

## Unknowns

> [!warning] UNVERIFIED — export mechanism + per-company-user throttle name not yet specified (medium security finding open).

- `*(assumed)*` route `/it/reporting`; current-vs-historical period boundary drives the cache TTL selector.

## Related

- [[../_module|IT Reporting]] · [[asset-valuation-widget]] · [[licence-spend-widget]] · [[helpdesk-metrics-widget]] · [[compliance-widget]]
- [[../architecture|it-reporting.architecture]] · [[../security|it-reporting.security]]
- [[../../../../architecture/patterns/custom-pages]] · [[../../../../security/data-ownership]]
