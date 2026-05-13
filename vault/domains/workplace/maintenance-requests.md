---
type: module
domain: Workplace & Facility
panel: workplace
module-key: workplace.maintenance
status: planned
color: "#4ADE80"
---

# Maintenance Requests

> Facility maintenance request submission, assignment to the facilities team, priority management, and completion tracking.

**Panel:** `workplace`
**Module key:** `workplace.maintenance`

---

## What It Does

Maintenance Requests gives employees a simple way to report facility issues — a broken chair, a faulty projector, a leaking tap — which are routed directly to the facilities team. Each request captures the location (building, floor, specific space), issue type, description, and optional photo evidence. Facilities managers assign requests to team members, set priorities, and track resolution. Requesters receive status updates at each stage. Completion data feeds into occupancy analytics for space quality reporting.

---

## Features

### Core
- Request submission: issue category, building/floor/space selection, description, and optional photo upload
- Priority levels: critical, high, medium, low — with configurable SLA targets per priority
- Assignment: facilities manager assigns requests to team members
- Status workflow: open → assigned → in progress → resolved → closed
- Requester notifications: status change notifications sent to the employee who submitted the request
- Resolution notes: technician adds completion notes and any parts or costs incurred

### Advanced
- Recurring maintenance tasks: schedule preventive maintenance on a regular cadence for specific spaces
- Contractor management: assign requests to external contractors with contact details and PO reference
- Maintenance cost tracking: log labour hours and materials cost per request for budget reporting
- Bulk request import: upload multiple requests from a CSV for planned maintenance campaigns
- Escalation rules: auto-escalate unassigned or stalled requests after a configurable time

### AI-Powered
- Issue categorisation: AI classifies the issue type from the description text to pre-fill the category
- Predictive maintenance: flag assets or spaces with recurring request patterns for proactive attention
- Priority suggestion: AI recommends a priority level based on description keywords and historical patterns

---

## Data Model

```erDiagram
    maintenance_requests {
        ulid id PK
        ulid company_id FK
        ulid space_id FK
        ulid submitted_by FK
        ulid assigned_to FK
        string category
        text description
        string priority
        string status
        json photo_urls
        text resolution_notes
        decimal cost
        timestamp resolved_at
        timestamps created_at_updated_at
    }

    maintenance_request_updates {
        ulid id PK
        ulid request_id FK
        ulid updated_by FK
        string from_status
        string to_status
        text note
        timestamp created_at
    }

    maintenance_requests ||--o{ maintenance_request_updates : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `maintenance_requests` | Request records | `id`, `company_id`, `space_id`, `submitted_by`, `assigned_to`, `priority`, `status`, `cost` |
| `maintenance_request_updates` | Status change history | `id`, `request_id`, `from_status`, `to_status`, `note` |

---

## Permissions

```
workplace.maintenance.submit
workplace.maintenance.view-own
workplace.maintenance.assign
workplace.maintenance.resolve
workplace.maintenance.view-all
```

---

## Filament

- **Resource:** `App\Filament\Workplace\Resources\MaintenanceRequestResource`
- **Pages:** `ListMaintenanceRequests`, `CreateMaintenanceRequest`, `EditMaintenanceRequest`, `ViewMaintenanceRequest`
- **Custom pages:** `MaintenanceQueuePage`, `PreventiveSchedulePage`
- **Widgets:** `OpenRequestsWidget`, `OverdueRequestsWidget`, `ResolutionTimeWidget`
- **Nav group:** Maintenance

---

## Displaces

| Feature | FlowFlex | Robin | OfficeSpace | Standalone CMMS |
|---|---|---|---|---|
| Employee request submission | Yes | No | No | Partial |
| SLA tracking | Yes | No | No | Yes |
| Preventive maintenance schedule | Yes | No | No | Yes |
| AI issue categorisation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[office-spaces]] — requests reference specific space records
- [[occupancy-analytics]] — maintenance data informs space quality metrics
- [[field-service/INDEX]] — for companies with an external field service team
