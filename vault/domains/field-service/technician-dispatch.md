---
type: module
domain: Field Service Management
panel: field
module-key: field.dispatch
status: planned
color: "#4ADE80"
---

# Technician Dispatch

> Field technician scheduling and dispatch — live map view, drag-and-drop job assignment, and route optimisation.

**Panel:** `field`
**Module key:** `field.dispatch`

---

## What It Does

Technician Dispatch gives dispatch coordinators a real-time view of all technicians and unassigned jobs on a single screen. Jobs can be dragged onto a technician's schedule from the unassigned pool. The dispatch board shows each technician's current location (via mobile app GPS), their booked jobs for the day in time order, and travel time between stops. Route optimisation recalculates the most efficient sequence of jobs for a technician's day with a single click, minimising drive time and fuel cost.

---

## Features

### Core
- Dispatch board: split view with unassigned jobs panel and technician day columns
- Drag-and-drop assignment: drag a job onto a technician's column to assign and schedule
- Technician list: all active field technicians with current status (available, on-site, travelling, off-shift)
- Map view: live map showing technician GPS locations and job site pins
- Job card preview: hover or click a job to see full details without leaving the board
- Daily schedule view: each technician's jobs in time sequence with travel buffer

### Advanced
- Route optimisation: reorder a technician's jobs to minimise total travel time for the day
- Skill matching: automatically filter available technicians by the skills required for the job
- Capacity view: weekly view of technician job load for forward planning
- Emergency job insertion: add urgent jobs to a technician's day with automatic re-sequencing
- Technician availability calendar: block out annual leave, training, and non-field days
- Multi-day jobs: span a work order across multiple technician days

### AI-Powered
- Intelligent auto-assign: AI assigns all unscheduled jobs to available technicians based on skill, proximity, and capacity
- Travel time prediction: predict journey time using traffic data and historical travel patterns
- Demand forecasting: forecast next week's job volume by region to plan technician rosters in advance

---

## Data Model

```erDiagram
    technician_profiles {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string name
        string status
        json skill_ids
        string home_postcode
        decimal current_lat
        decimal current_lng
        timestamps created_at_updated_at
    }

    dispatch_assignments {
        ulid id PK
        ulid company_id FK
        ulid work_order_id FK
        ulid technician_id FK
        date assigned_date
        time assigned_start
        time assigned_end
        integer sequence_order
        string travel_route_data
        timestamps created_at_updated_at
    }

    technician_profiles ||--o{ dispatch_assignments : "receives"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `technician_profiles` | Technician records | `id`, `company_id`, `user_id`, `name`, `status`, `skill_ids`, `current_lat`, `current_lng` |
| `dispatch_assignments` | Job-to-technician schedule | `id`, `work_order_id`, `technician_id`, `assigned_date`, `assigned_start`, `sequence_order` |

---

## Permissions

```
field.dispatch.view
field.dispatch.assign
field.dispatch.reassign
field.dispatch.view-map
field.dispatch.manage-technicians
```

---

## Filament

- **Resource:** None (custom page only)
- **Pages:** N/A
- **Custom pages:** `DispatchBoardPage`, `DispatchMapPage`, `TechnicianCapacityPage`
- **Widgets:** `UnassignedJobsWidget`, `TechnicianStatusWidget`
- **Nav group:** Dispatch

---

## Displaces

| Feature | FlowFlex | ServiceTitan | FieldAware | Jobber |
|---|---|---|---|---|
| Live dispatch board | Yes | Yes | Yes | Yes |
| Map view with GPS | Yes | Yes | Yes | Yes |
| Route optimisation | Yes | Yes | Yes | Partial |
| AI auto-assign | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Filament:** `DispatchBoardPage` is a full custom `Page` — a split-screen layout with an unassigned jobs panel on the left and technician day columns on the right. This is the most complex UI in the field service domain. Drag-and-drop from the unassigned pool to a technician column uses SortableJS cross-list dragging, with the drop event calling `Livewire::dispatch('jobAssigned', {jobId, technicianId, date, startTime})`.

**`DispatchMapPage`:** This is a custom `Page` rendering a live map of technician GPS positions and job site pins. **External dependency — mapping library (must be decided before build):**
1. **Google Maps JavaScript API:** High accuracy, familiar UX. Requires `GOOGLE_MAPS_API_KEY` in `.env`. Per-load pricing — can become expensive at high usage.
2. **Mapbox GL JS (MIT):** Strong developer experience. `MAPBOX_ACCESS_TOKEN` in `.env`. Free tier: 50,000 map loads/month.
3. **Leaflet.js + OpenStreetMap tiles:** Fully open source, no API key for basic tiles. Less polished but zero cost.

**Recommended:** Mapbox GL JS for the map component. The map is rendered in a `<div id="dispatch-map">` in the Blade view, initialised with JavaScript. Technician positions (`current_lat`, `current_lng`) are updated in real time via Reverb.

**Real-time GPS updates:** Technicians update their position via the mobile app (React Native/Capacitor — noted as TBD in tech stack). The mobile app calls `PATCH /api/v1/field/technician-location` every 30 seconds. The API endpoint updates `technician_profiles.current_lat` and `current_lng` and broadcasts `TechnicianLocationUpdated` on `field.dispatch.{company_id}` private channel. The `DispatchMapPage` Livewire component listens via Reverb Echo and updates the map marker position in JavaScript.

**Route optimisation:** The spec mentions route optimisation. This requires an external routing API:
- **Google Maps Routes API:** `computeRoutes` endpoint with waypoints. Returns optimised waypoint order.
- **OpenRouteService (free, self-hostable):** Open-source alternative.

For MVP, implement a simple nearest-neighbour heuristic in PHP (no external API) — it won't be optimal but gives a fast result. Add Google Routes API as a Phase 2 enhancement.

**AI auto-assign:** Calls `app/Services/AI/DispatchAutoAssignService.php` with a list of unassigned jobs (location, required skills, estimated duration) and available technicians (current location, remaining capacity, skills). The service calls OpenAI GPT-4o with a structured prompt returning a JSON assignment map. The result is previewed by the dispatcher before confirming — it does not auto-apply.

## Related

- [[work-orders]] — dispatch board sources from work order schedule
- [[customer-assets]] — job site location derived from asset or customer address
- [[service-level-agreements]] — SLA response deadlines shown on dispatch board
