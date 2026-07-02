---
domain: customer-success
module: playbooks
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Playbooks — DTOs & API

## DTOs

### CreatePlaybookData (input)

| Field | Type | Validation |
|---|---|---|
| name | string | required |
| trigger_type | string | required; in `manual, health-drop, renewal, new-customer` |
| trigger_config | array | required-per-type (e.g. renewal window days; health tier) |
| steps | array | min 1; each `{title, description?, owner_role in [csm,manager], day_offset ≥0, order}` |

### RunPlaybookData (input)

| Field | Type | Validation |
|---|---|---|
| playbook_id | ulid | required; exists, `is_active`, tenant-scoped |
| account_id | ulid | required; exists (CRM); **no active run** for this playbook+account |

### PlaybookRunData (output)

`run_id`, `playbook_name`, `account_id`, `status`, `started_at`, `steps[]` (each `{title, status, due_date, assignee_id}`), `progress`

---

## Internal Read API

`PlaybookService::run` is called in-process by `cs.churn`'s `RunRecoveryPlaybookAction` (one-click at-risk recovery). Run/step completion stats are read by `cs.analytics` (playbook effectiveness, health delta after run). No HTTP surface.

---

## Public / Portal Endpoints

None. Playbooks are internal CS operations inside the authenticated `/crm` panel.
