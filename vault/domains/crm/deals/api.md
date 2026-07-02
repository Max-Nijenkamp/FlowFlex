---
domain: crm
module: deals
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Deals — DTOs & API

## DTOs

### CreateDealData (input)

| Field | Type | Validation |
|---|---|---|
| name | string | required, max:255 |
| account_id | ?string | nullable, ulid, exists in company |
| contact_id | ?string | nullable, ulid, exists in company |
| owner_id | string | required, ulid, exists in company |
| stage_id | string | required, ulid, exists in company, not a won/lost stage |
| value_cents | int | required, min:0 |
| currency | string | required, size:3, valid ISO 4217 |
| probability | ?float | nullable, between:0,100 — defaults from stage |
| expected_close_date | ?CarbonImmutable | nullable, date, after_or_equal:today *(assumed)* |

Cross-field: at least one of `account_id` / `contact_id` required ("A deal needs an account or a contact") *(assumed)*.

---

### UpdateDealData

Same fields as `CreateDealData`, all optional (partial update).

---

### CloseDealData (input)

| Field | Type | Validation |
|---|---|---|
| deal_id | string | required, ulid |
| outcome | string | required, in:won,lost |
| lost_reason | ?string | required_if:outcome,lost, max:1000 |
| lost_to | ?string | nullable, max:255 |

---

### DealData (output)

`id`, `name`, `account_id`, `contact_id`, `owner_id`, `owner_name`, `stage_id`, `stage_name`, `value_cents`, `currency`, `value_formatted`, `probability`, `weighted_value_cents` (computed), `expected_close_date`, `actual_close_date`, `status`, `days_in_stage` (computed), `lost_reason`

---

## Public / Portal Endpoints

No public API endpoints planned for v1. All access is via the Filament CRM panel (authenticated, `crm` guard).
