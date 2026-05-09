---
type: module
domain: Operations & Supply Chain
panel: operations
phase: 3
status: planned
cssclasses: domain-operations
migration_range: 457000–457499
last_updated: 2026-05-09
---

# Equipment Maintenance (CMMS)

Computerised Maintenance Management System. Track assets, schedule preventive maintenance, manage work orders, and reduce unplanned downtime.

---

## Asset Register

All physical equipment tracked:
- Asset ID, name, type, location
- Make, model, serial number, purchase date, warranty
- Maintenance history: all work orders completed
- Mean Time Between Failures (MTBF) tracked
- Current status: operational / under maintenance / out of service

---

## Preventive Maintenance (PM)

Scheduled maintenance programmes:
- Time-based: every 30 days, every 6 months, annually
- Usage-based: every 1,000 hours, every 10,000 units produced
- Condition-based: when sensor reading exceeds threshold (IoT integration)

PM schedules auto-generate work orders before due date.

---

## Work Orders

Created from:
- PM schedule (planned)
- Staff breakdown report (unplanned/reactive)
- Inspection finding

Work order contains:
- Equipment, problem description, priority
- Assigned technician(s)
- Parts required (pulls from parts inventory)
- Estimated vs actual labour hours
- Completion checklist
- Attach photos, notes

---

## Parts Inventory

Spare parts tracking:
- Parts catalogue with minimum stock levels
- On work order creation: check parts availability
- Auto-reorder when parts fall below reorder point
- Links to Procurement for parts POs

---

## Downtime Tracking

For production-critical equipment:
- Log downtime events: start, end, cause, category (planned/unplanned)
- OEE (Overall Equipment Effectiveness) calculation
- Downtime cost: hours × production value per hour

---

## Data Model

### `ops_equipment`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(300) | |
| category | varchar(100) | |
| location | varchar(200) | |
| serial_number | varchar(100) | nullable |
| status | enum | operational/maintenance/out_of_service |

### `ops_work_orders`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| equipment_id | ulid | FK |
| type | enum | preventive/reactive/inspection |
| priority | enum | low/medium/high/critical |
| status | enum | pending/in_progress/completed/cancelled |
| assigned_to | ulid | nullable FK |
| scheduled_date | date | nullable |
| completed_at | timestamp | nullable |

---

## Migration

```
457000_create_ops_equipment_table
457001_create_ops_work_orders_table
457002_create_ops_pm_schedules_table
457003_create_ops_downtime_events_table
```

---

## Related

- [[MOC_Operations]]
- [[warehouse-management]]
- [[MOC_Procurement]] — parts reordering
