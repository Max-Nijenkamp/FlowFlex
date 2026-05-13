---
type: module
domain: Real Estate & Property
panel: realestate
module-key: realestate.maintenance
status: planned
color: "#4ADE80"
---

# Property Maintenance

> Maintenance requests against properties — issue type, contractor assignment, cost tracking, and resolution management.

**Panel:** `realestate`
**Module key:** `realestate.maintenance`

---

## What It Does

Property Maintenance manages the repair and upkeep lifecycle for properties in the portfolio. Tenants or property managers raise maintenance requests against a specific property or unit. Requests are categorised (structural, mechanical, electrical, cosmetic, health and safety), assigned to an internal team member or external contractor, and tracked through to resolution. All costs are logged against the request, providing a maintenance cost record per property that informs service charge reconciliation and planned capital expenditure decisions.

---

## Features

### Core
- Request creation: property/unit, issue category, description, severity (emergency, urgent, routine), photo upload
- Contractor assignment: assign to an internal team member or select an external contractor from the supplier register
- Status workflow: open → assigned → works in progress → completed → closed
- Cost recording: log labour costs, materials, and contractor invoices against each request
- Tenant notification: notify the tenant when works are scheduled and when completed
- Maintenance history per property: full log of all past maintenance with costs

### Advanced
- Planned preventive maintenance: schedule recurring inspections and maintenance tasks (annual fire alarm test, gutter clearing)
- Contractor performance tracking: rate contractors on quality, responsiveness, and value for money
- SLA tracking: configurable response and resolution time targets per severity level
- Service charge allocation: flag whether a repair cost is tenant-recharge or landlord-borne
- Warranty tracking: record equipment warranties and alert before they expire

### AI-Powered
- Issue categorisation: AI categorises the issue from the free-text description
- Cost estimation: estimate repair cost based on issue type and property size using historical data
- Recurring issue detection: flag properties with repetitive issues in the same category as candidates for capital repair

---

## Data Model

```erDiagram
    property_maintenance_requests {
        ulid id PK
        ulid property_id FK
        ulid unit_id FK
        ulid company_id FK
        ulid raised_by FK
        ulid assigned_to FK
        string category
        text description
        string severity
        string status
        json photo_urls
        decimal total_cost
        boolean is_tenant_recharge
        timestamp resolved_at
        timestamps created_at_updated_at
    }

    maintenance_cost_lines {
        ulid id PK
        ulid request_id FK
        string description
        string cost_type
        decimal amount
        string invoice_reference
        timestamps created_at_updated_at
    }

    property_maintenance_requests ||--o{ maintenance_cost_lines : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `property_maintenance_requests` | Maintenance records | `id`, `property_id`, `unit_id`, `category`, `severity`, `status`, `total_cost`, `is_tenant_recharge` |
| `maintenance_cost_lines` | Cost breakdown | `id`, `request_id`, `cost_type`, `amount`, `invoice_reference` |

---

## Permissions

```
realestate.maintenance.submit
realestate.maintenance.assign
realestate.maintenance.resolve
realestate.maintenance.view-all
realestate.maintenance.view-costs
```

---

## Filament

- **Resource:** `App\Filament\Realestate\Resources\PropertyMaintenanceResource`
- **Pages:** `ListPropertyMaintenanceRequests`, `CreatePropertyMaintenanceRequest`, `ViewPropertyMaintenanceRequest`
- **Custom pages:** `MaintenanceQueuePage`, `PreventiveSchedulePage`, `PropertyMaintenanceHistoryPage`
- **Widgets:** `OpenRequestsWidget`, `MaintenanceCostWidget`
- **Nav group:** Maintenance

---

## Displaces

| Feature | FlowFlex | Yardi | MRI | Re-Leased |
|---|---|---|---|---|
| Maintenance request workflow | Yes | Yes | Yes | Yes |
| Cost tracking per property | Yes | Yes | Yes | Yes |
| Preventive maintenance | Yes | Yes | Yes | Partial |
| AI issue categorisation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[property-register]] — requests reference property records
- [[tenant-occupancy-management]] — tenant communications on maintenance status
- [[rental-billing-arrears]] — tenant-recharge maintenance costs added to billing
