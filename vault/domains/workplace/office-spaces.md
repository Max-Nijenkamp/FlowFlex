---
type: module
domain: Workplace & Facility
panel: workplace
module-key: workplace.spaces
status: planned
color: "#4ADE80"
---

# Office Spaces

> Physical office space registry — floors, zones, individual desks, meeting rooms, capacity, and amenities.

**Panel:** `workplace`
**Module key:** `workplace.spaces`

---

## What It Does

Office Spaces is the master registry of physical workspace resources. Facility managers define the office hierarchy — buildings, floors, and zones — then create individual desk and meeting room records within each zone. Each space record holds capacity, amenities (whiteboard, video conferencing, standing desk), and booking availability rules. This registry is the foundation that the Desk Booking module queries when displaying available resources to employees.

---

## Features

### Core
- Building and floor hierarchy: create buildings → floors → zones
- Desk records: individual desk with zone, floor, equipment tags, and hot-desk/assigned-desk designation
- Meeting room records: name, capacity, floor, amenities (projector, whiteboard, video conferencing), bookable flag
- Floor plan upload: upload a floor plan image and pin spaces to it visually
- Availability rules: set operating hours, minimum booking advance notice, and maximum booking duration per resource
- Space status: mark spaces as available, unavailable, or under maintenance

### Advanced
- Zone types: open plan, quiet zone, collaboration hub, phone booth
- Equipment tracking: link specific hardware (monitors, docking stations) to desk records
- Neighbourhood assignment: assign desks to departments or teams for neighbourhood-based seating
- Capacity limits: enforce maximum occupancy per zone or floor (e.g. COVID-era density rules)
- Bulk import: import a list of desks and rooms from a CSV template

### AI-Powered
- Space recommendation: suggest the best desk or room for a booking based on requester preferences and past choices
- Underutilisation alerts: flag spaces consistently booked at low rates for potential consolidation
- Optimal layout suggestions: analyse occupancy patterns and suggest zone reconfigurations

---

## Data Model

```erDiagram
    office_buildings {
        ulid id PK
        ulid company_id FK
        string name
        string address
        integer total_floors
        timestamps created_at_updated_at
    }

    office_floors {
        ulid id PK
        ulid building_id FK
        integer floor_number
        string label
        string floor_plan_url
        timestamps created_at_updated_at
    }

    office_spaces {
        ulid id PK
        ulid floor_id FK
        ulid company_id FK
        string name
        string type
        integer capacity
        json amenities
        boolean is_bookable
        string status
        timestamps created_at_updated_at
    }

    office_buildings ||--o{ office_floors : "has"
    office_floors ||--o{ office_spaces : "contains"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `office_buildings` | Building definitions | `id`, `company_id`, `name`, `address` |
| `office_floors` | Floor definitions | `id`, `building_id`, `floor_number`, `floor_plan_url` |
| `office_spaces` | Desk and room records | `id`, `floor_id`, `name`, `type`, `capacity`, `amenities`, `is_bookable`, `status` |

---

## Permissions

```
workplace.spaces.view
workplace.spaces.create
workplace.spaces.update
workplace.spaces.delete
workplace.spaces.manage-availability
```

---

## Filament

- **Resource:** `App\Filament\Workplace\Resources\OfficeSpaceResource`
- **Pages:** `ListOfficeSpaces`, `CreateOfficeSpace`, `EditOfficeSpace`
- **Custom pages:** `FloorPlanViewPage` (interactive floor plan), `SpaceInventoryPage`
- **Widgets:** `SpaceAvailabilityWidget`, `TotalCapacityWidget`
- **Nav group:** Spaces

---

## Displaces

| Feature | FlowFlex | Robin | OfficeSpace | Condeco |
|---|---|---|---|---|
| Floor hierarchy | Yes | Yes | Yes | Yes |
| Floor plan upload | Yes | Yes | Yes | Yes |
| AI layout suggestions | Yes | No | No | No |
| Native HR integration | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[desk-booking]] — booking queries this space registry
- [[occupancy-analytics]] — occupancy data sourced from space records
- [[maintenance-requests]] — maintenance requests reference specific spaces
