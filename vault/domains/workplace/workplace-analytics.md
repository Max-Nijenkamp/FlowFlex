---
type: module
domain: Workplace & Facility
panel: workplace
module-key: workplace.analytics
status: planned
color: "#4ADE80"
---

# Workplace Analytics

Room and desk utilisation, occupancy trends, visitor volume, and maintenance metrics for facility planning.

## Core Features

- Room utilisation: booking rate, no-show rate, peak hours
- Desk utilisation: occupancy %, hybrid attendance patterns
- Occupancy trends: how full the office is over time
- Visitor volume trends
- Maintenance metrics: request volume, resolution time, by category
- Space optimisation insights (underused rooms/desks)
- Export reports

## Data Model

No additional tables. Aggregates from `wp_room_bookings`, `wp_desk_bookings`, `wp_visitors`, `wp_maintenance_requests`.

## Filament

**Nav group:** Analytics

- `WorkplaceDashboardPage` (custom dashboard) — chart widgets

## Related

- [[domains/workplace/room-booking]]
- [[domains/workplace/desk-booking]]
- [[architecture/performance]]
