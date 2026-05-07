---
tags: [flowflex, domain/operations, maintenance, cmms, phase/4]
domain: Operations
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-07
---

# Equipment Maintenance (CMMS)

Preventive and reactive maintenance management for all physical assets — schedule services, dispatch work orders, track parts and labour costs.

**Who uses it:** Maintenance technicians, operations managers, facility managers
**Filament Panel:** `operations`
**Depends on:** [[Asset Management]], [[Inventory Management]]
**Phase:** 4
**Build complexity:** High — 4 resources, 2 pages, 4 tables

---

## Features

- **Preventive maintenance schedules** — define time-based or usage-based (odometer/run-hours) service triggers per asset, with configurable frequency and frequency unit (days/weeks/months/km/hours)
- **Reactive work orders** — raise unplanned work orders from fault reports or ad-hoc discoveries, assign to technician immediately
- **Automated scheduling** — system auto-generates the next work order when `next_due_at` is reached or a threshold is breached
- **Work order lifecycle** — open → in_progress → completed/cancelled with timestamps and labour hour logging
- **Priority management** — critical/high/medium/low priority for work orders; critical orders surface in dashboard alerts
- **Parts tracking** — technicians log parts consumed per work order; inventory is decremented via `POSTransactionCompleted` / direct deduction event
- **Labour cost capture** — log hours worked per work order; multiply by technician rate to compute job cost
- **Maintenance history per asset** — full chronological log of every service event, reading, and note
- **Downtime tracking** — log unplanned downtime start/end per asset to compute MTTR and availability %
- **Technician assignment** — link work orders to a workspace tenant; integrates with [[Field Service Management]] if active
- **Mobile-friendly fault reporting** — field staff can raise a reactive work order from their phone with photo and description
- **Next service date propagation** — on work order completion, `next_due_at` on the schedule is recalculated and `last_done_at` is updated
- **Email/in-app alerts** — notify assigned technician on dispatch; notify ops manager when overdue orders exist
- **Export to PDF** — work order summary PDF for handover or compliance records

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `maintenance_schedules`
| Column | Type | Notes |
|---|---|---|
| `asset_id` | ulid FK | → assets |
| `type` | enum | `preventive`, `reactive` |
| `name` | string | schedule label |
| `description` | text nullable | |
| `frequency` | integer | numeric value |
| `frequency_unit` | enum | `days`, `weeks`, `months`, `km`, `hours` |
| `last_done_at` | timestamp nullable | |
| `next_due_at` | timestamp nullable | computed on completion |
| `assigned_technician_id` | ulid FK nullable | → tenants (default technician) |
| `is_active` | boolean | default true |
| `notes` | text nullable | |

### `work_orders`
| Column | Type | Notes |
|---|---|---|
| `asset_id` | ulid FK | → assets |
| `maintenance_schedule_id` | ulid FK nullable | null for ad-hoc reactive |
| `tenant_id` | ulid FK nullable | assigned technician → tenants |
| `status` | enum | `open`, `in_progress`, `completed`, `cancelled` |
| `priority` | enum | `critical`, `high`, `medium`, `low` |
| `title` | string | |
| `description` | text nullable | |
| `started_at` | timestamp nullable | |
| `completed_at` | timestamp nullable | |
| `labour_hours` | decimal(6,2) nullable | |
| `labour_cost` | decimal(10,2) nullable | |
| `total_parts_cost` | decimal(10,2) nullable | |
| `total_cost` | decimal(10,2) nullable | computed |
| `notes` | text nullable | |

### `work_order_parts`
| Column | Type | Notes |
|---|---|---|
| `work_order_id` | ulid FK | → work_orders |
| `product_id` | ulid FK | → products (inventory item) |
| `quantity_used` | decimal(10,3) | |
| `unit_cost` | decimal(10,2) | cost at time of use |
| `total_cost` | decimal(10,2) | computed |
| `notes` | string nullable | |

### `maintenance_logs`
| Column | Type | Notes |
|---|---|---|
| `asset_id` | ulid FK | → assets |
| `work_order_id` | ulid FK nullable | |
| `tenant_id` | ulid FK nullable | who logged this |
| `notes` | text nullable | |
| `odometer_reading` | decimal(10,2) nullable | km or hours at time of service |
| `next_service_due` | timestamp nullable | |
| `attachments` | json nullable | file IDs array |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `MaintenanceScheduled` | `work_order_id`, `asset_id`, `tenant_id` | Notification to assigned technician |
| `WorkOrderCompleted` | `work_order_id`, `asset_id` | [[Inventory Management]] (confirm parts deduction), activity log |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `AssetCheckedIn` | [[Asset Management]] | Check if any maintenance schedule `next_due_at` is due; if so, auto-generate a work order |

---

## Permissions

```
operations.maintenance-schedules.view
operations.maintenance-schedules.create
operations.maintenance-schedules.edit
operations.maintenance-schedules.delete
operations.work-orders.view
operations.work-orders.create
operations.work-orders.edit
operations.work-orders.delete
operations.work-orders.complete
operations.maintenance-logs.view
operations.maintenance-logs.create
```

---

## Related

- [[Operations Overview]]
- [[Asset Management]]
- [[Field Service Management]]
- [[Inventory Management]]
- [[Quality Control & Inspections]]
