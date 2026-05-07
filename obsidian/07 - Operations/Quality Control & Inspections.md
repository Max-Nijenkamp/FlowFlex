---
tags: [flowflex, domain/operations, quality, inspections, phase/4]
domain: Operations
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-07
---

# Quality Control & Inspections

Digital inspection checklists, pass/fail scoring, and non-conformance management. Build templates once, run them anywhere — assets, batches, supplier deliveries, or site audits.

**Who uses it:** Quality managers, inspectors, operations managers, field technicians
**Filament Panel:** `operations`
**Depends on:** [[Asset Management]], Core
**Phase:** 4
**Build complexity:** High — 4 resources, 1 page, 5 tables

---

## Features

- **Inspection template builder** — create reusable templates with configurable item types: pass/fail, text, number, or photo capture
- **Template versioning** — publish new versions without breaking historic records; old records reference the template version used
- **Asset-linked inspections** — run an inspection against a specific asset (equipment, vehicle, facility area) to maintain per-asset inspection history
- **Mobile inspection execution** — field staff complete inspections on phone or tablet; photo evidence captured in-app and stored to S3
- **Pass/fail scoring** — each item contributes to an overall inspection score; configurable pass mark per template
- **Non-conformance reports (NCR)** — raise an NCR directly from any failed inspection item; set severity (minor/major/critical) and assign for resolution
- **Corrective action tracking** — link corrective actions to NCRs; track owner, due date, and resolution status
- **ISO audit readiness** — pre-built templates for ISO 9001, ISO 14001 style audits; export inspection records as PDF evidence
- **Inspection scheduling** — link inspection templates to maintenance schedules or run on-demand
- **Dashboard metrics** — pass rate trends, open NCRs by severity, overdue inspections widget
- **Notification on critical failure** — `InspectionFailed` event fires when score falls below pass mark; notifies ops manager immediately
- **Bulk inspection export** — export all records for a date range to CSV or PDF for external audits
- **Supplier delivery inspections** — run a quality inspection on goods received; link to purchase order and flag NCR to procurement

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `inspection_templates`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `type` | string | e.g. "Asset Safety Check", "Goods Received" |
| `description` | text nullable | |
| `pass_mark` | integer default 100 | minimum score % to pass |
| `version` | integer default 1 | |
| `is_active` | boolean | default true |

### `inspection_template_items`
| Column | Type | Notes |
|---|---|---|
| `inspection_template_id` | ulid FK | → inspection_templates |
| `question` | string | |
| `type` | enum | `pass_fail`, `text`, `number`, `photo` |
| `is_required` | boolean | default true |
| `sort_order` | integer | |
| `weight` | integer default 1 | score weighting |
| `help_text` | string nullable | guidance for inspector |

### `inspection_records`
| Column | Type | Notes |
|---|---|---|
| `inspection_template_id` | ulid FK | → inspection_templates |
| `template_version` | integer | snapshot of version used |
| `asset_id` | ulid FK nullable | → assets |
| `tenant_id` | ulid FK | who conducted it → tenants |
| `status` | enum | `in_progress`, `completed`, `voided` |
| `conducted_at` | timestamp | |
| `score` | integer nullable | % score, computed on completion |
| `passed` | boolean nullable | |
| `notes` | text nullable | |
| `reference` | string nullable | e.g. batch number, delivery ref |

### `inspection_responses`
| Column | Type | Notes |
|---|---|---|
| `inspection_record_id` | ulid FK | → inspection_records |
| `inspection_template_item_id` | ulid FK | → inspection_template_items |
| `response` | text nullable | text or number value |
| `passed` | boolean nullable | for pass_fail type |
| `file_id` | ulid FK nullable | → files (photo evidence) |
| `notes` | string nullable | |

### `non_conformance_reports`
| Column | Type | Notes |
|---|---|---|
| `inspection_record_id` | ulid FK nullable | → inspection_records |
| `tenant_id` | ulid FK | raised by → tenants |
| `assigned_to` | ulid FK nullable | → tenants |
| `description` | text | |
| `severity` | enum | `minor`, `major`, `critical` |
| `status` | enum | `open`, `under_review`, `resolved` |
| `corrective_action` | text nullable | |
| `resolved_at` | timestamp nullable | |
| `resolved_by` | ulid FK nullable | → tenants |
| `asset_id` | ulid FK nullable | → assets |
| `evidence_file_ids` | json nullable | array of file IDs |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `InspectionFailed` | `inspection_record_id`, `score`, `asset_id` | Notification to operations manager; optionally creates maintenance work order |

---

## Events Consumed

None — Quality Control is triggered manually or by schedule.

---

## Permissions

```
operations.inspection-templates.view
operations.inspection-templates.create
operations.inspection-templates.edit
operations.inspection-templates.delete
operations.inspection-records.view
operations.inspection-records.create
operations.inspection-records.edit
operations.inspection-records.delete
operations.non-conformance-reports.view
operations.non-conformance-reports.create
operations.non-conformance-reports.edit
operations.non-conformance-reports.resolve
```

---

## Related

- [[Operations Overview]]
- [[Asset Management]]
- [[Equipment Maintenance]]
- [[HSE]]
- [[Purchasing & Procurement]]
