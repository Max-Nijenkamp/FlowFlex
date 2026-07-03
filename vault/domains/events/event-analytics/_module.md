---
domain: events
module: event-analytics
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Event Analytics

Registration funnel, attendance rate, ticket revenue, and sponsor ROI per event. Owns **no tables** — pure read aggregation.

## Module-key

| Field | Value |
|---|---|
| key | `events.analytics` |
| priority | p3 |
| panel | events |
| permission-prefix | `events.analytics` |
| tables | *(none)* |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../events/_module\|events.events]] | Core metrics |
| Hard | [[../registrations/_module\|events.registrations]] | Funnel + attendance |
| Hard | [[../../core/billing/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Soft | [[../tickets/_module\|events.tickets]] | Revenue section (hidden when inactive) |
| Soft | [[../sponsors/_module\|events.sponsors]] | Sponsorship revenue section (hidden when inactive) |

## Core Features

- **Registration funnel** — views → registrations → confirmed → attended.
- **Attendance / no-show rate** — attended / confirmed.
- **Ticket revenue** — per event and per ticket type.
- **Sponsorship revenue** — per event by tier.
- **Session popularity** — check-ins per session deferred; attendance proxy *(assumed)*.
- **Across-events comparison** + report export.

## See features/

- [[features/event-dashboard|Event Dashboard]] — the analytics dashboard page (selector, comparison, export).

## Build Manifest

```
app/Data/Events/EventMetricsData.php
app/Services/Events/EventAnalyticsService.php
app/Filament/Events/Pages/EventAnalyticsDashboard.php
app/Filament/Events/Widgets/{FunnelWidget,RevenueWidget,AttendanceWidget}.php
tests/Feature/Events/EventAnalyticsTest.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's event analytics data
- [ ] Module gating: artifacts hidden when `events.event-analytics` inactive
- [ ] Funnel + attendance math over fixtures.
- [ ] Revenue via brick/money; sections hidden when modules inactive.
- [ ] Across-events comparison.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | events, registrations, ticket purchases, sponsors | events.* | Read-only aggregation across sibling modules |

**Data ownership:** `events.analytics` owns **no tables** and writes nothing. It reads `ev_events`, `ev_registrations`, `ev_ticket_purchases`, `ev_sponsors` through their owning services / read models — a pure read consumer. Revenue sections are hidden when the source module is inactive ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../events/_module|Events]] · [[../registrations/_module|Registrations]] · [[../../../architecture/caching]]
- [[../_index|Events MOC]]
