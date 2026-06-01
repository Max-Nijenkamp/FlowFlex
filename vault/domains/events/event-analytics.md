---
type: module
domain: Events Management
panel: events
module-key: events.analytics
status: planned
color: "#4ADE80"
---

# Event Analytics

Registration trends, attendance rate, ticket revenue, and sponsor ROI per event.

## Core Features

- Registration funnel: views → registrations → confirmed → attended
- Attendance rate (attended / registered)
- No-show rate
- Ticket revenue per event and per ticket type
- Sponsorship revenue
- Session popularity (which sessions drew most attendees)
- Registration source breakdown
- Across-events comparison
- Export reports

## Data Model

No additional tables. Aggregates from `ev_events`, `ev_registrations`, `ev_ticket_purchases`, `ev_sponsors`.

## Filament

**Nav group:** Analytics

- `EventAnalyticsDashboard` (custom dashboard) — chart widgets

## Related

- [[domains/events/events]]
- [[domains/events/registrations]]
- [[architecture/performance]]
