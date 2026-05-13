---
type: module
domain: Workplace & Facility Management
panel: workplace
cssclasses: domain-workplace
phase: 6
status: complete
migration_range: 868000–869999
last_updated: 2026-05-12
---

# Workplace Analytics

Space utilisation dashboards, occupancy trends, cost-per-desk calculations, and ROI on office space. Inputs from all other Workplace modules.

---

## Metrics Tracked

### Space Utilisation
- **Desk utilisation rate**: booked desk-days / available desk-days (per floor, building, company)
- **Peak occupancy hours**: heat-map of when most people are in office (by day of week)
- **Ghost bookings**: booked but no check-in (waste indicator)
- **Neighbourhood utilisation**: how busy each team area is
- **Floor utilisation**: if a floor is consistently under 40% → flag for consolidation

### Meeting Room Utilisation
- **Room booking rate**: booked hours / available hours
- **No-show rate**: rooms booked but not checked in
- **Average size vs capacity**: if 12-person room always booked by 2 people → right-sizing recommendation
- **Popular vs underused rooms**: rank by booking frequency

### Visitor Volume
- Visitors per day/week/month
- Peak visitor days and times
- Visitor type breakdown (guest/contractor/candidate)

### Maintenance
- Open vs resolved tickets per category
- Average resolution time vs SLA target
- Maintenance cost by category and location

---

## Dashboard Views

### Executive Dashboard
- Monthly office attendance rate (% of employees who came in at least 1× per week)
- Total cost per occupied desk per month
- Real estate utilisation efficiency score (0–100)
- YoY trend comparison

### Facilities Manager Dashboard
- Today's desk availability (live)
- This week's room availability
- Open maintenance tickets (by priority/SLA status)
- Visitor log today

### Space Planning View
- Suggested desk reduction if utilisation < 60%
- Recommended neighbourhood merges
- "Empty real estate" — floors or zones with < 30% usage

---

## Cost Analysis

Cost-per-desk calculation:
```
Monthly office cost (rent + utilities + cleaning + maintenance)
÷ Average occupied desks per month
= Cost per occupied desk per month
```

Inputs from Finance module (rent/utilities) if connected; otherwise manual entry.

Department cost allocation: if neighbourhood booking is linked to a department → apportion cost to that department's cost centre.

---

## Data Architecture

Pre-computed nightly in `workplace_metric_snapshots` table (same pattern as HR analytics):

| Metric | Grain | Computed From |
|---|---|---|
| desk_utilisation | per floor, per day | desk_bookings with check-in |
| room_utilisation | per room, per day | room_bookings with check-in |
| ghost_booking_rate | per floor, per week | bookings with no check-in |
| visitor_volume | per building, per day | visitor_visits |
| maintenance_sla_rate | per category, per week | maintenance_requests |

---

## Migration

```
868000_create_workplace_metric_snapshots_table
868001_create_workplace_analytics_config_table
```

---

## Related

- [[MOC_Workplace]]
- [[hot-desk-space-booking]] — source: desk bookings + check-ins
- [[meeting-room-management]] — source: room bookings
- [[visitor-management]] — source: visitor log
- [[facility-maintenance-requests]] — source: maintenance tickets
- [[MOC_Analytics]] — rolls up into company-wide analytics
