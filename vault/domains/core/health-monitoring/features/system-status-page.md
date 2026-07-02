---
domain: core
module: health-monitoring
feature: system-status-page
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: System Status Page

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

The simplified, owner-facing health surface in `/app` — a custom Filament page showing green/red per registered health check with a last-checked timestamp, polling every 60s. It reads the same `spatie/laravel-health` results as [[health-endpoint]] but presents them to a company owner instead of a machine or FlowFlex staff.

## UI

- **Kind**: custom-page — `SystemStatusPage`, a custom Filament page in the `/app` panel (`system-status.blade.php`).
- **Page**: `SystemStatusPage` (`/app` panel, owner). Route: Filament custom-page route under `/app`. View: `resources/views/filament/app/pages/system-status.blade.php`.
- **Layout**: a list/grid of health checks, each rendered green (ok) or red (failing), with an overall status and a "last checked" timestamp.
- **Key interactions**: owner opens the page → sees per-check status → page polls every 60s and re-renders check states. Read-only, no actions.
- **States**: empty (n/a — checks always registered) · loading (initial + 60s poll refresh) · error (a failing check → that row renders red; page still renders) · selected (n/a — read-only list).
- **Gating**: `canAccess() = Auth::user()->can('core.health.view-any') && BillingService::hasModule('core.health')` — owner-only, and only if the company has the `core.health` module active. See [[../security]].

## Data

- Owns / writes: **no tables of its own, no writes.** Health results are ephemeral (evaluated by `spatie/laravel-health`, cached in the health store).
- Reads: the `spatie/laravel-health` check results (infrastructure liveness) only — no domain/business tables. Calls `BillingService::hasModule('core.health')` read-only for the gate (owned by [[../../billing-engine/_module]]).
- Cross-domain writes: none — effects other domains only via events (there are none) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events) — reads health-check results directly.
- Feeds: none.
- Shared entity: `spatie/laravel-health` results (owned by this module's config wiring); `BillingService::hasModule` gate (owned by [[../../billing-engine/_module]], read-only).

## Related

- [[../_module]] · [[../architecture]] · [[../security]] · [[health-endpoint]]
- [[../../billing-engine/_module]] · [[../../../../security/data-ownership]] · [[../../../../architecture/filament-patterns]]
