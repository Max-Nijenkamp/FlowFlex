---
domain: customer-success
module: qbr
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# QBR — DTOs & API

## DTOs

### ScheduleQbrData (input)

| Field | Type | Validation |
|---|---|---|
| account_id | ulid | required; exists (CRM), tenant-scoped |
| scheduled_at | datetime | required; **future** |
| csm_id | ulid | required; account owner *(assumed)* |
| agenda | string | nullable; defaults from template |

### RecordOutcomesData (input)

| Field | Type | Validation |
|---|---|---|
| qbr_id | ulid | required; exists, status `scheduled` |
| outcomes | string | **required** (needed to mark held) |
| action_items | array | each `{description, owner_id, due_date (future)}` |

### QbrDeckData (output)

`account_id`, `health_trend?`, `support_summary?`, `deal_contract_overview?` (each section present only when its source module is active), `prepared_at`

---

## Internal Read API

QBR completion + action-item stats can be read by `cs.analytics` (QBR cadence adherence) — in-process, tenant-scoped. No HTTP surface.

---

## Public / Portal Endpoints

None. QBRs are internal CS operations inside the authenticated `/crm` panel. (A future customer-facing QBR portal is noted as an opportunity, not built v1 — [[../../_opportunities]].)
