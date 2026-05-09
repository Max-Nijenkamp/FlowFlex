---
type: moc
domain: Workplace & Facility Management
panel: workplace
cssclasses: domain-workplace
phase: 6
color: "#0F766E"
last_updated: 2026-05-09
---

# Workplace & Facility Management — Map of Content

Office space management for hybrid teams. Desk booking, meeting room reservations, visitor management, facility maintenance requests, and office resource management. Replaces Robin, OfficeSpace, Envoy, and Condeco.

**Panel:** `workplace`  
**Phase:** 6  
**Migration Range:** `850000–869999`  
**Colour:** Teal `#0F766E` / Light: `#F0FDFA`  
**Icon:** `heroicon-o-building-office`

---

## Why This Domain Exists

Hybrid work is the default. Every company with 10+ employees in 2026 needs desk booking. Current solutions charge €5–10/desk/month (Robin = €300/mo for 30 desks). FlowFlex includes it, plus it integrates with HR (who's in office), Operations (facilities maintenance), and Communications (room booking from calendar).

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Hot Desk & Space Booking | 6 | planned | Interactive floor plan, desk reservation, check-in/no-show |
| Meeting Room Management | 6 | planned | Room booking, display panels, AV resources, utilisation |
| Visitor Management | 6 | planned | Pre-register visitors, sign-in kiosk, badge printing, host notifications |
| Facility Maintenance Requests | 6 | planned | Staff raise maintenance tickets, contractor assignment, SLA tracking |
| Office Resource Management | 6 | planned | Parking, lockers, equipment loans (laptops, projectors) |
| Workplace Analytics | 6 | planned | Space utilisation, peak hours, cost-per-desk, ROI on office space |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `DeskBooked` | Space Booking | Notifications (confirmation), HR (who's in office today) |
| `VisitorArrived` | Visitor Management | Notifications (host), IT (guest wifi code) |
| `FacilityRequestRaised` | Maintenance Requests | Notifications (facilities manager) |
| `EmployeeOffboarded` | HR (consumed) | Workplace (cancel future bookings, release desk assignment) |
| `MeetingRoomBooked` | Room Management | Communications (add to calendar) |

---

## Filament Panel Structure

**Navigation Groups:**
- `Spaces` — Floor Plans, Desks, Meeting Rooms, Parking
- `Bookings` — Desk Bookings, Room Bookings, Resource Loans
- `Visitors` — Visitor Log, Pre-registrations
- `Facilities` — Maintenance Requests, Contractors
- `Analytics` — Space Utilisation, Occupancy Reports

---

## Permissions Prefix

`workplace.spaces.*` · `workplace.bookings.*` · `workplace.visitors.*`  
`workplace.facilities.*` · `workplace.analytics.*`

---

## Competitors Displaced

Robin · OfficeSpace · Envoy · Condeco · Skedda · Joan (room booking)

---

## Related

- [[MOC_Domains]]
- [[MOC_HR]] — office attendance, offboarding
- [[MOC_Communications]] — calendar sync for room bookings
- [[MOC_Operations]] — facilities maintenance shared patterns
