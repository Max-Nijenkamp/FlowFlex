---
domain: analytics
module: kpi-tracking
feature: threshold-alerts
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Threshold Alerts

Notify the KPI owner when a KPI's captured actual falls below target — once per period.

## Behaviour

- After snapshot capture, `KpiService::status()` evaluates the band; a below-target result triggers an alert.
- Alert is dispatched via the **notifications service** ([[../../core/notifications/_module|core.notifications]]) — Analytics never writes notification tables.
- `bi_kpi_snapshots.alerted` is the once-guard: at most one alert per `(kpi, period)`.
- Manual KPIs alert only after a value is entered and evaluated.

## UI

- **Kind**: background — no page of its own. The resulting notification surfaces in the recipient's in-app notification centre (owned by `core.notifications`).
- **Page**: none.
- **Layout**: n/a.
- **Key interactions**: n/a (fires from the capture job).
- **States**: n/a (delivery/read state lives in notifications).
- **Gating**: dispatched to the KPI `owner_id` (+ roles with `analytics.kpis.view-any` *(assumed)*); no UI permission of its own.

## Data

- Owns / writes: sets `bi_kpi_snapshots.alerted` (own table) as the once-guard.
- Reads: `bi_kpis` + the just-captured snapshot.
- Cross-domain writes: none — the notification is created by the notifications service via a service call ([[../../../../security/data-ownership]]).

## Relations

- Consumes: breach signal from [[snapshot-capture]].
- Feeds: `NotificationService::notify(...)` → [[../../core/notifications/_module|core.notifications]] writes its own tables.
- Shared entity: recipient users (referenced by id).

## Unknowns

- Recipients beyond the owner, channel, and escalation on sustained breach — see [[../unknowns]].

## Related

- [[../_module|KPI Tracking]] · [[snapshot-capture]] · [[../../core/notifications/_module|core.notifications]]
