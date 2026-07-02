---
domain: crm
module: contracts
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Contracts — API & DTOs

## Input DTOs

### CreateContractData

| Field | Type | Rules |
|---|---|---|
| account_id | ulid | Required. |
| deal_id | ulid? | Optional. |
| title | string | Required. |
| value_cents | int | ≥ 0. |
| billing_interval | string | one-off / monthly / yearly. |
| start_date | date | Required. |
| end_date | date | After `start_date`. |
| auto_renew | bool | |
| notice_period_days | int | Default 30. |

### TerminateContractData

| Field | Type | Rules |
|---|---|---|
| contract_id | ulid | Required. |
| reason | string | Required. |

## Output DTOs

### ContractData

Full contract projection — account, deal, title, value (Money), billing interval, dates, renewal terms, status, `signed_at`.

## Public / Portal Endpoints

None. Contracts are managed inside the `/crm` panel only.

See [[../../../architecture/patterns/dto-pattern]].
