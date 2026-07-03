---
domain: support
module: support-analytics
feature: support-dashboard
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Support Dashboard

The at-a-glance performance view: volume, response/resolution times, CSAT, agent performance, SLA compliance, backlog, busy hours.

## Behaviour

- Date-range filter drives `SupportAnalyticsService::metrics(from, to)` (cached).
- Widgets: ticket volume (created vs resolved), CSAT, agent performance, busy-hours heat-map; SLA compliance widget shown only when [[../../sla/_module|support.sla]] is active.
- Widgets poll every 60s.

## UI

- **Kind**: custom-page — `SupportDashboardPage` with apex-chart widgets (dashboard, not table+form).
- **Page**: "Support Dashboard" (`/support/dashboard`) — Filament dashboard page (ui-strategy row #6) + `leandrocfe/filament-apex-charts`.
- **Layout**: date-range header; grid of widgets (volume line, CSAT gauge, agent table, busy-hours heat-map, SLA compliance).
- **Key interactions**: change date range → widgets refresh; hover charts for detail; 60s poll.
- **States**: empty (no tickets in range → "no data yet") · loading (widget skeletons) · error (query fails → retry per widget) · selected (date range applied).
- **Gating**: `support.analytics.view`.

## Data

- Owns / writes: nothing (read-only dashboard; CSAT rows written by the survey listener).
- Reads: `sup_tickets`, `sup_ticket_replies`, `sup_sla_events`, `sup_csat_responses` (aggregate).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: metrics aggregated from Tickets + SLA tables (read-only).
- Feeds: nothing.
- Shared entity: `sup_tickets` / `sup_sla_events` (read).

## Test Checklist

### Unit
- [ ] Metric math over fixtures: CSAT average, first-response/resolution averages, per-agent aggregates
- [ ] Backlog trend counts open tickets per day correctly

### Feature (Pest)
- [ ] `SupportAnalyticsService::metrics` scopes strictly to the current company (no cross-tenant leak) and caches per `company_id`
- [ ] Date-range filter changes the aggregated result; cached key namespaced by range
- [ ] SLA compliance widget omitted when `support.sla` is inactive

### Livewire
- [ ] `SupportDashboardPage` `canAccess()` denies without `support.analytics.view` / inactive module
- [ ] Changing the date range refreshes the widgets

## Unknowns

- Cache "current vs historical" boundary; reassignment CSAT attribution — [[../unknowns]].

## Related

- [[../_module|Support Analytics]] · [[./csat-survey]]
