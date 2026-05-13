---
type: module
domain: Field Service Management
panel: field
module-key: field.slas
status: planned
color: "#4ADE80"
---

# Service Level Agreements

> SLA definitions, response and resolution time tracking, breach alerting, and compliance reporting.

**Panel:** `field`
**Module key:** `field.slas`

---

## What It Does

Service Level Agreements defines the contractual response and resolution time commitments made to customers. Each SLA is configured with priority tiers and time targets — for example, a P1 fault must receive a response within 4 hours and be resolved within 8 hours. SLAs are linked to customers or asset types. When a work order is created, the system automatically applies the correct SLA based on priority and customer, starts the SLA clock, and tracks time elapsed. If a response or resolution deadline is approaching, the system escalates with alerts to the dispatcher and manager. SLA compliance statistics feed the job invoicing and reporting modules.

---

## Features

### Core
- SLA definition: name, priority tiers (P1–P4), response time target, and resolution time target per tier
- Business hours: configure working hours and public holidays for SLA clock calculation
- SLA assignment: link SLAs to specific customers, contract types, or asset categories
- Automatic SLA application: when a work order is created, the applicable SLA and deadlines are set automatically
- SLA clock: live countdown showing time remaining to response and resolution deadlines
- Breach tracking: record SLA breaches with root cause for compliance reporting

### Advanced
- Escalation rules: auto-notify dispatcher, manager, or account manager as deadline approaches (e.g. at 50%, 75%, and 100% of target)
- SLA pause: pause the SLA clock when waiting for customer access or parts and resume on re-engagement
- Contract SLAs: read SLA entitlement from the customer's active maintenance contract
- Custom priority labels: rename priority tiers to match customer contract terminology
- SLA exemption: mark individual work orders as SLA-exempt with reason (e.g. customer-caused delay)

### AI-Powered
- Breach risk prediction: flag work orders likely to breach SLA based on current technician availability and job queue
- Optimal dispatch for SLA: factor SLA deadlines into auto-assign logic to prioritise high-risk jobs
- SLA performance trend: identify customers, regions, or job types with consistently poor SLA compliance

---

## Data Model

```erDiagram
    sla_definitions {
        ulid id PK
        ulid company_id FK
        string name
        json business_hours
        json public_holidays
        timestamps created_at_updated_at
    }

    sla_tiers {
        ulid id PK
        ulid company_id FK
        ulid sla_id FK
        string priority_label
        integer priority_level
        integer response_minutes
        integer resolution_minutes
        timestamps created_at_updated_at
    }

    work_order_sla_records {
        ulid id PK
        ulid company_id FK
        ulid work_order_id FK
        ulid sla_id FK
        ulid sla_tier_id FK
        timestamp response_deadline
        timestamp resolution_deadline
        timestamp responded_at
        timestamp resolved_at
        boolean response_breached
        boolean resolution_breached
        integer pause_minutes
        timestamps created_at_updated_at
    }

    sla_definitions ||--o{ sla_tiers : "has"
    sla_definitions ||--o{ work_order_sla_records : "applied via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `sla_definitions` | SLA configurations | `id`, `company_id`, `name`, `business_hours`, `public_holidays` |
| `sla_tiers` | Priority tier time targets | `id`, `sla_id`, `priority_label`, `response_minutes`, `resolution_minutes` |
| `work_order_sla_records` | Per-job SLA tracking | `id`, `work_order_id`, `sla_tier_id`, `response_deadline`, `resolution_deadline`, `response_breached`, `resolution_breached` |

---

## Permissions

```
field.slas.view
field.slas.manage
field.slas.view-compliance
field.slas.pause
field.slas.export
```

---

## Filament

- **Resource:** `App\Filament\Field\Resources\SlaDefinitionResource`
- **Pages:** `ListSlaDefinitions`, `CreateSlaDefinition`, `EditSlaDefinition`
- **Custom pages:** `SlaComplianceDashboardPage`, `SlaBreachLogPage`
- **Widgets:** `SlaComplianceRateWidget`, `ActiveBreachesWidget`
- **Nav group:** Work Orders

---

## Displaces

| Feature | FlowFlex | ServiceTitan | FieldAware | Jobber |
|---|---|---|---|---|
| SLA time targets | Yes | Yes | Yes | No |
| Breach tracking and alerts | Yes | Yes | Yes | No |
| Business hours clock | Yes | Yes | Yes | No |
| AI breach risk prediction | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[work-orders]] — SLA clock attached to each work order
- [[technician-dispatch]] — SLA deadlines factor into dispatch priority
- [[customer-assets]] — asset type may determine applicable SLA tier
- [[job-invoicing]] — SLA breach credits applied to invoice where contractually required
