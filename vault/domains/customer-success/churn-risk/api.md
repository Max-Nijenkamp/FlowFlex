---
domain: customer-success
module: churn-risk
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Churn Risk — DTOs & API

## DTOs

### ResolveRiskData (input)

| Field | Type | Validation |
|---|---|---|
| risk_id | ulid | required; exists, open (`resolved_at IS NULL`), tenant-scoped |
| note | string | nullable; resolution reason |

Manual resolution *(assumed)*; automatic resolution needs no DTO (handled inside `evaluate`).

### ChurnRiskData (output)

`account_id`, `account_name`, `risk_level`, `risk_factors[]` (each `{factor, detail}`), `assigned_csm_id`, `detected_at`, `resolved_at`

---

## Internal Read API

`ChurnRiskService::evaluate` and the open-risk list are consumed in-process by `cs.analytics` (at-risk count, recovery rate) and by `cs.playbooks` (health-drop auto-trigger reads the same signal). No HTTP surface — tenant-scoped in-process calls only.

---

## Public / Portal Endpoints

None. Churn risk is an internal CS signal, exposed only inside the authenticated `/crm` panel.
