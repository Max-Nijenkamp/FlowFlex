---
type: module
domain: Field Service Management
panel: field
module-key: field.work-orders
status: planned
color: "#4ADE80"
---

# Work Orders

> Field work order management — create, schedule, assign, and track jobs from open to close.

**Panel:** `field`
**Module key:** `field.work-orders`

---

## What It Does

Work Orders is the operational core of the field service panel. Service managers create work orders against customer assets or locations, set the job type, priority, and required skills, and schedule the job for a specific date and time window. Technicians receive jobs on their mobile device, update status in the field, capture photos, collect customer signatures, and mark jobs complete. The system tracks every status transition with timestamps for SLA compliance and billing purposes.

---

## Features

### Core
- Work order creation: customer, location, asset, job type, description, priority, and required skills
- Scheduling: assign a planned date and time window (e.g. 09:00–12:00)
- Status workflow: draft → scheduled → in-progress → complete → invoiced
- Technician assignment: assign one or more technicians to a job
- Mobile-friendly job card: technicians view job details, asset history, and parts needed
- Photo capture: attach before/during/after photos to work orders
- Customer signature: collect digital sign-off on job completion
- Job notes: technician field notes appended to the work order record

### Advanced
- Recurring work orders: auto-generate work orders on a schedule for maintenance contracts
- Sub-tasks: break a work order into discrete checklist steps with completion tracking
- Parts used: log parts consumed from van or warehouse stock against the work order
- Customer notification: automated SMS/email to customer when technician is en route and on arrival
- Follow-up work orders: raise a linked follow-up order when additional work is identified on-site
- Custom fields: configure job-type-specific fields for data capture

### AI-Powered
- Job duration estimation: predict likely job duration based on job type, asset age, and technician history
- Optimal scheduling suggestion: recommend the best time slot balancing technician availability and travel time
- Fault diagnosis hints: surface relevant knowledge base articles based on asset type and fault description

---

## Data Model

```erDiagram
    work_orders {
        ulid id PK
        ulid company_id FK
        ulid customer_id FK
        ulid asset_id FK
        ulid assigned_technician_id FK
        string reference
        string job_type
        string priority
        string status
        text description
        date scheduled_date
        time scheduled_start
        time scheduled_end
        timestamp started_at
        timestamp completed_at
        timestamps created_at_updated_at
    }

    work_order_notes {
        ulid id PK
        ulid work_order_id FK
        ulid company_id FK
        ulid author_id FK
        text note
        string note_type
        timestamps created_at_updated_at
    }

    work_order_parts {
        ulid id PK
        ulid work_order_id FK
        ulid company_id FK
        ulid part_id FK
        integer quantity_used
        decimal unit_cost
        timestamps created_at_updated_at
    }

    work_orders ||--o{ work_order_notes : "has"
    work_orders ||--o{ work_order_parts : "consumes"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `work_orders` | Job records | `id`, `company_id`, `customer_id`, `asset_id`, `reference`, `job_type`, `status`, `scheduled_date`, `completed_at` |
| `work_order_notes` | Field notes and updates | `id`, `work_order_id`, `author_id`, `note`, `note_type` |
| `work_order_parts` | Parts consumed per job | `id`, `work_order_id`, `part_id`, `quantity_used`, `unit_cost` |

---

## Permissions

```
field.work-orders.view-own
field.work-orders.view-all
field.work-orders.create
field.work-orders.manage
field.work-orders.close
```

---

## Filament

- **Resource:** `App\Filament\Field\Resources\WorkOrderResource`
- **Pages:** `ListWorkOrders`, `CreateWorkOrder`, `EditWorkOrder`, `ViewWorkOrder`
- **Custom pages:** `WorkOrderKanbanPage`, `WorkOrderCalendarPage`
- **Widgets:** `OpenWorkOrdersWidget`, `OverdueJobsWidget`, `TechnicianUtilisationWidget`
- **Nav group:** Work Orders

---

## Displaces

| Feature | FlowFlex | ServiceTitan | FieldAware | Jobber |
|---|---|---|---|---|
| Work order lifecycle | Yes | Yes | Yes | Yes |
| Digital signature capture | Yes | Yes | Yes | Yes |
| Photo attachment | Yes | Yes | Yes | Yes |
| AI duration estimation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[technician-dispatch]] — dispatch view reads work order schedule
- [[customer-assets]] — work orders linked to assets for service history
- [[service-level-agreements]] — SLA breach tracked against work order response and resolution times
- [[job-invoicing]] — invoice generated from completed work order
- [[part-inventory]] — parts consumed logged against van stock
