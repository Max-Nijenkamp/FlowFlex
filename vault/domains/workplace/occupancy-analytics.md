---
type: module
domain: Workplace & Facility
panel: workplace
module-key: workplace.analytics
status: planned
color: "#4ADE80"
---

# Occupancy Analytics

> Read-only occupancy intelligence — peak hours, desk utilisation rates, space efficiency, and capacity planning insights.

**Panel:** `workplace`
**Module key:** `workplace.analytics`

---

## What It Does

Occupancy Analytics aggregates data from desk bookings, check-ins, and visitor logs to give facility managers a clear picture of how their office space is actually used. Dashboards show utilisation rates per desk, zone, floor, and building over configurable time periods, highlight peak occupancy hours, and identify chronically underused spaces. This data drives evidence-based decisions about office footprint — whether to reduce real estate costs, reconfigure zones, or invest in additional capacity.

---

## Features

### Core
- Desk utilisation rate: percentage of available desk-days that were actually booked and checked in
- Zone utilisation heatmap: colour-coded floor plan showing utilisation by zone
- Peak occupancy hours: hourly breakdown of office occupancy across the working week
- Booking vs check-in rate: measure of no-show frequency per space
- Building and floor comparison: side-by-side utilisation across multiple locations
- Time-period filtering: view metrics for any date range (last week, last month, last quarter)

### Advanced
- Trend analysis: week-on-week and month-on-month utilisation trends
- Department utilisation: breakdown of desk usage by department or team
- Space efficiency score: composite score factoring in utilisation, booking lead time, and no-show rate
- Real estate cost modelling: estimate savings from consolidating underused floors or buildings
- Visitor volume trends: track visitor traffic as part of overall building occupancy

### AI-Powered
- Capacity recommendation: suggest optimal desk-to-employee ratio based on historical utilisation patterns
- Underused space identification: automatically surface spaces below a utilisation threshold
- Demand forecasting: predict expected occupancy for future weeks based on historical patterns and company calendar events

---

## Data Model

```erDiagram
    occupancy_snapshots {
        ulid id PK
        ulid company_id FK
        ulid space_id FK
        date snapshot_date
        integer hour
        integer booked_count
        integer checked_in_count
        decimal utilisation_rate
        timestamps created_at_updated_at
    }

    space_utilisation_summaries {
        ulid id PK
        ulid company_id FK
        ulid space_id FK
        string period_type
        date period_start
        date period_end
        decimal avg_utilisation_rate
        integer total_bookings
        integer total_no_shows
    }

    occupancy_snapshots }o--|| office_spaces : "measures"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `occupancy_snapshots` | Hourly occupancy data | `id`, `company_id`, `space_id`, `snapshot_date`, `hour`, `utilisation_rate` |
| `space_utilisation_summaries` | Aggregated summaries | `id`, `space_id`, `period_type`, `avg_utilisation_rate`, `total_no_shows` |

---

## Permissions

```
workplace.analytics.view
workplace.analytics.view-all-buildings
workplace.analytics.view-department
workplace.analytics.export
workplace.analytics.view-cost-modelling
```

---

## Filament

- **Resource:** None (read-only, no CRUD)
- **Pages:** N/A
- **Custom pages:** `OccupancyDashboardPage`, `SpaceEfficiencyPage`, `DemandForecastPage`
- **Widgets:** `UtilisationHeatmapWidget`, `PeakHoursWidget`, `NoShowRateWidget`, `BuildingComparisonWidget`
- **Nav group:** Spaces

---

## Displaces

| Feature | FlowFlex | Robin | OfficeSpace | Density.io |
|---|---|---|---|---|
| Booking vs check-in analytics | Yes | Yes | Yes | Yes |
| Zone heatmaps | Yes | Yes | Yes | Yes |
| Real estate cost modelling | Yes | No | Yes | No |
| AI capacity recommendations | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[desk-booking]] — booking data is the primary source
- [[visitor-management]] — visitor volume included in occupancy
- [[office-spaces]] — space metadata (capacity, zone) used in calculations
