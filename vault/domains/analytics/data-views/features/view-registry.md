---
domain: analytics
module: data-views
feature: view-registry
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# View Registry

The catalogue of shipped `DataView` classes, filtered to the views whose source modules are all active for the company.

## Behaviour

- Each shipped view registers itself: `DataViewRegistry::register(RevenuePerRepView::class)`.
- `available()` returns only views where every `requiredModules()` entry passes `BillingService::hasModule(...)`.
- Views are FlowFlex code, never user-authored — no query surface to inject into.
- The registry powers the gallery ([[view-explorer]]) and drill-down ([[drill-down]]).

## UI

- **Kind**: background — no page. In-memory registry populated at boot; surfaced by [[view-explorer]].
- **Page**: none.
- **Layout**: n/a.
- **Key interactions**: n/a (registration in code).
- **States**: n/a.
- **Gating**: a view is exposed only when all its source modules are active; no permission of its own.

## Data

- Owns / writes: nothing — in-memory registry.
- Reads: nothing itself; the registered views read their source domains at run time.
- Cross-domain writes: none, ever ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `BillingService::hasModule()` from [[../../../core/billing-engine/_module|core.billing]] for the active-module filter.
- Feeds: available view list to [[view-explorer]]; view classes to [[drill-down]] and [[view-export]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] `available()` filters out any view with one inactive `requiredModules()` entry

### Feature (Pest)
- [ ] Registered views appear for a company with all source modules active; disappear on deactivation
- [ ] Registry exposes only shipped classes — no user-authored query path (arch assertion)

## Unknowns

- `*(assumed)*` — plain singleton registry, no Interface→Service binding for v1.
- Final shipped view set — see [[../unknowns]].

## Related

- [[../_module|Cross-Domain Data Views]] · [[view-explorer]] · [[../../dashboards/features/metric-registry|MetricRegistry (sibling pattern)]]
