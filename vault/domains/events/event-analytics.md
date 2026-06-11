---
type: module
domain: Events Management
domain-key: events
panel: events
module-key: events.analytics
status: planned
priority: p3
depends-on: [events.events, events.registrations, core.billing, core.rbac]
soft-depends: [events.tickets, events.sponsors]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: []
permission-prefix: events.analytics
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Event Analytics

Registration trends, attendance rate, ticket revenue, and sponsor ROI per event. Owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/events/events\|events.events]] + [[domains/events/registrations\|events.registrations]] | core metrics |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | tickets / sponsors | revenue sections hidden when inactive |

---

## Core Features

- Registration funnel: views → registrations → confirmed → attended
- Attendance rate (attended / confirmed) + no-show rate
- Ticket revenue per event and per ticket type
- Sponsorship revenue
- Session popularity (check-ins per session deferred — attendance proxy *(assumed)*)
- Across-events comparison
- Export reports

---

## Data Model

No additional tables. Aggregates from `ev_events`, `ev_registrations`, `ev_ticket_purchases`, `ev_sponsors`.

## DTOs

Output only: `EventMetricsData`.

## Services & Actions

- `EventAnalyticsService::metrics(?string $eventId, from, to): EventMetricsData` — brick/money revenue; soft sections conditional

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:events:metrics:{event}:{range}` | 1 h past events / 15 min live | TTL only |

---

## Filament

**Nav group:** Analytics

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `EventAnalyticsDashboard` | #6 dashboard page + apex charts | event selector, comparison view, export |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('events.analytics.view-any') && BillingService::hasModule('events.analytics')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a throttle (e.g. RateLimiter on the export action) for analytics report exports.

---

## Permissions

`events.analytics.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Funnel + attendance math over fixtures
- [ ] Revenue via brick/money; sections hidden when modules inactive
- [ ] Across-events comparison

---

## Build Manifest

```
app/Data/Events/EventMetricsData.php
app/Services/Events/EventAnalyticsService.php
app/Filament/Events/Pages/EventAnalyticsDashboard.php
app/Filament/Events/Widgets/{FunnelWidget,RevenueWidget,AttendanceWidget}.php
tests/Feature/Events/EventAnalyticsTest.php
```

---

## Related

- [[domains/events/events]]
- [[domains/events/registrations]]
- [[architecture/caching]]
