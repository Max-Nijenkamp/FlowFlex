---
tags: [flowflex, domain/operations, field-service, dispatch, phase/4]
domain: Operations
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-07
---

# Field Service Management

Dispatch technicians, track them live, and get digital job sign-off on mobile. Bridges the gap between the back-office and field workers.

**Who uses it:** Dispatch coordinators, field technicians, operations managers, customers (notifications)
**Filament Panel:** `operations`
**Depends on:** [[Asset Management]], [[CRM — Contact & Company Management]], [[Inventory Management]]
**Phase:** 4
**Build complexity:** Very High — 5 resources, 3 pages, 9 tables

---

## Features

- **Job creation and scheduling** — create field jobs from CRM tickets, manual requests, or maintenance work orders; assign date/time windows
- **Technician dispatch** — assign one or more technicians per job; send push/email notification on dispatch
- **Live GPS tracking** — real-time location feed from the mobile app stored in `technician_locations`; show all technicians on map widget
- **Route optimisation** — suggest shortest drive order for multi-stop days; store optimised route in `job_routes`
- **SLA and time-window management** — define response SLA per job priority; alert dispatch when an SLA window is at risk
- **Parts used on-site** — technicians log parts consumed from inventory during the job; auto-deducts stock via `WorkOrderPartsUsed` event
- **On-site photo capture** — technicians take photos per job step; stored to S3, linked via `job_photos`
- **Job completion checklists** — template-based checklists (e.g. "electrical safety check") completed per job in `job_checklist_responses`
- **Customer digital sign-off** — customer signs on technician's mobile device; signature image stored to S3, linked via `field_job_signatures`
- **Customer arrival notification** — automated SMS/email to customer when technician is en route
- **Mobile app (offline-capable)** — technician can complete jobs, log parts, and capture signatures without network; syncs on reconnect
- **Invoice trigger** — `FieldJobCompleted` event automatically creates a draft invoice in [[Invoicing]] for labour + parts
- **Field job status board** — Kanban board view in Filament: unassigned → dispatched → in_progress → completed
- **Integration with Equipment Maintenance** — field jobs can be linked to work orders; completion updates maintenance log

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `field_jobs`
| Column | Type | Notes |
|---|---|---|
| `crm_contact_id` | ulid FK nullable | → crm_contacts |
| `crm_company_id` | ulid FK nullable | → crm_companies |
| `work_order_id` | ulid FK nullable | → work_orders (if maintenance linked) |
| `title` | string | |
| `description` | text nullable | |
| `status` | enum | `unassigned`, `dispatched`, `en_route`, `on_site`, `completed`, `cancelled` |
| `priority` | enum | `critical`, `high`, `medium`, `low` |
| `scheduled_start_at` | timestamp nullable | |
| `scheduled_end_at` | timestamp nullable | |
| `actual_start_at` | timestamp nullable | |
| `actual_end_at` | timestamp nullable | |
| `address` | string nullable | job site address |
| `location_lat` | decimal(10,7) nullable | |
| `location_lng` | decimal(10,7) nullable | |
| `sla_due_at` | timestamp nullable | |
| `notes` | text nullable | |

### `field_job_technicians`
| Column | Type | Notes |
|---|---|---|
| `field_job_id` | ulid FK | → field_jobs |
| `tenant_id` | ulid FK | → tenants |
| `role` | enum | `lead`, `assistant` |
| `dispatched_at` | timestamp nullable | |
| `accepted_at` | timestamp nullable | |

### `field_job_parts`
| Column | Type | Notes |
|---|---|---|
| `field_job_id` | ulid FK | → field_jobs |
| `product_id` | ulid FK | → products |
| `quantity_used` | decimal(10,3) | |
| `unit_cost` | decimal(10,2) | |
| `unit_price` | decimal(10,2) | charge-out price |
| `total_cost` | decimal(10,2) | |
| `total_price` | decimal(10,2) | |

### `field_job_signatures`
| Column | Type | Notes |
|---|---|---|
| `field_job_id` | ulid FK | → field_jobs |
| `signatory_name` | string | customer name |
| `signatory_email` | string nullable | |
| `file_id` | ulid FK | → files (S3 signature image) |
| `signed_at` | timestamp | |
| `ip_address` | string nullable | |

### `technician_locations`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | → tenants |
| `field_job_id` | ulid FK nullable | current active job |
| `latitude` | decimal(10,7) | |
| `longitude` | decimal(10,7) | |
| `accuracy_metres` | integer nullable | |
| `recorded_at` | timestamp | |

### `job_routes`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | → tenants |
| `date` | date | |
| `job_sequence` | json | ordered array of field_job_ids |
| `estimated_distance_km` | decimal(8,2) nullable | |
| `estimated_duration_minutes` | integer nullable | |
| `optimised_at` | timestamp | |

### `job_photos`
| Column | Type | Notes |
|---|---|---|
| `field_job_id` | ulid FK | → field_jobs |
| `tenant_id` | ulid FK | who took it |
| `file_id` | ulid FK | → files |
| `caption` | string nullable | |
| `taken_at` | timestamp | |
| `step` | string nullable | e.g. "before", "during", "after" |

### `job_checklists`
| Column | Type | Notes |
|---|---|---|
| `name` | string | template name |
| `description` | text nullable | |
| `items` | json | array of {label, required: bool} |
| `is_active` | boolean | default true |

### `job_checklist_responses`
| Column | Type | Notes |
|---|---|---|
| `field_job_id` | ulid FK | → field_jobs |
| `job_checklist_id` | ulid FK | → job_checklists |
| `tenant_id` | ulid FK | who completed |
| `responses` | json | array of {label, passed: bool, notes} |
| `completed_at` | timestamp | |
| `all_passed` | boolean | computed summary |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `FieldJobDispatched` | `field_job_id`, `tenant_ids` | Notification to assigned technicians, SMS to customer |
| `FieldJobCompleted` | `field_job_id` | [[Invoicing]] (create draft invoice), [[Inventory Management]] (confirm parts deduction), [[Customer Support & Helpdesk]] (close related ticket) |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `TicketCreated` | [[Customer Support & Helpdesk]] | If ticket type = `on_site_visit`, auto-create a field job and link to the ticket |

---

## Permissions

```
operations.field-jobs.view
operations.field-jobs.create
operations.field-jobs.edit
operations.field-jobs.delete
operations.field-jobs.dispatch
operations.field-jobs.complete
operations.job-checklists.view
operations.job-checklists.create
operations.job-checklists.edit
operations.job-checklists.delete
operations.technician-locations.view
```

---

## Related

- [[Operations Overview]]
- [[Asset Management]]
- [[Equipment Maintenance]]
- [[Inventory Management]]
- [[Invoicing]]
- [[Customer Support & Helpdesk]]
