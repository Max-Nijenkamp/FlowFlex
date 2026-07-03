---
domain: it
module: it-reporting
feature: asset-valuation-widget
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Asset Valuation Widget

## Purpose

Show total IT asset inventory value plus counts broken down by asset type and status. This is the one always-on widget (its source, `it.assets`, is the module's hard dependency).

## Behavior

- Total inventory value across all company assets (brick/money, integer minor units).
- Count and value grouped by asset **type** and by **status**.
- Rendered as apex-chart widgets on the IT dashboard under the shared header period filter.

## UI

- **Kind**: widget
- **Page**: hosted on the "IT Reporting" dashboard (`/it/reporting`) — ships apex-chart widgets, not a page of its own.
- **Layout**: a total-value stat plus a bar/pie of value + count by type and by status, in the dashboard grid under the shared header period filter.
- **Key interactions**: change the header period to re-scope; hover a series for the point tooltip.
- **States**: empty ("No assets yet" placeholder when count is 0) · loading (skeleton chart while aggregate/cache resolves) · error ("Couldn't load metrics" card with retry) · selected (hovered data point highlighted).
- **Gating**: visible with `it.reporting.view` and `it.reporting` module active.

## Data

- **Owns NOTHING** — read-only aggregation, no tables, no writes.
- Reads: `it_assets` (type, status, value) via the **it.assets** read API; aggregated in `ItAnalyticsService::metrics` → `asset_value_total`, `asset_breakdown[]`.
- **Cross-domain writes: none at all** — never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Reads from `it.assets` (read-only). Not soft-dep — this is the module's hard dependency, so the widget always renders.
- Consumes: nothing.
- Feeds: nothing (read-only).

## Test Checklist

### Unit
- [ ] Value aggregation groups by type and status with brick/money integer minor units (no float math)

### Feature (Pest)
- [ ] `ItAnalyticsService::metrics` computes asset totals in one grouped query (no per-row iteration)
- [ ] Tenant isolation: company A's aggregate never includes company B's assets (cache key embeds company_id)

### Livewire
- [ ] Widget renders totals; zero assets shows "No assets yet" placeholder; hidden without `it.reporting.view`

## Unknowns

- `*(assumed)*` asset "value" is a stored per-asset amount on `it_assets` (purchase/book value); depreciation is not modelled here.

## Related

- [[../_module|IT Reporting]] · [[it-dashboard]] · [[../../asset-inventory/_module|it.assets]]
- [[../architecture|it-reporting.architecture]] · [[../../../../security/data-ownership]]
