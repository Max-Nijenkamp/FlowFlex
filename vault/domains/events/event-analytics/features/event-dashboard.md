---
domain: events
module: event-analytics
feature: event-dashboard
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Event Dashboard

The analytics dashboard: registration funnel, attendance, revenue, and across-events comparison.

## Behaviour

- Select an event (or a date range / all-events) → renders the funnel (views → registered → confirmed → attended), attendance/no-show rate, ticket revenue (per type), and sponsor revenue (per tier).
- Revenue sections hide when Tickets/Sponsors are inactive.
- Across-events comparison; report export (throttled).

## UI

- **Kind**: custom-page
- **Page**: "Event Dashboard" (`/app/events/analytics`) — `EventAnalyticsDashboard`, ui-strategy row #6 + apex charts.
- **Layout**: event selector + date range at top; funnel chart, attendance donut, revenue bars (ticket + sponsor), comparison table; export button.
- **Key interactions**: change event/range → widgets refresh (cached); toggle comparison; export report.
- **States**: empty (no events → prompt) · loading (skeleton charts) · error (retry) · selected (event scoped; comparison active).
- **Gating**: `events.analytics.view`.

## Data

- Owns / writes: nothing.
- Reads: `ev_events`, `ev_registrations`, `ev_ticket_purchases`, `ev_sponsors` via their owning services (read-only).
- Cross-domain writes: NONE — pure read consumer ([[../../../../security/data-ownership]]).

## Relations

- Consumes: read aggregation across all sibling Events modules.
- Feeds: nothing.
- Shared entity: reads sibling read models; owns none.

## Test Checklist

### Unit
- [ ] Funnel math: registrations -> confirmed -> attended rates; revenue via brick/money integers

### Feature (Pest)
- [ ] Soft sections (tickets/sponsors) omitted without error when module inactive
- [ ] Tenant isolation: metrics per company; export/permission gated

### Livewire
- [ ] Dashboard page canAccess() explicit; event selector + range filter re-scope widgets

## Unknowns

- Source of landing-page views (top of funnel) — see [[../unknowns]].
- Sponsor ROI depth — see [[../unknowns]] + [[../../_opportunities]].

## Related

- [[../_module|Event Analytics]] · [[../../registrations/_module|Registrations]] · [[../../tickets/_module|Tickets]] · [[../../sponsors/_module|Sponsors]]
