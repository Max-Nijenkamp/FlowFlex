---
domain: crm
module: sales-sequences
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sales Sequences — API

## Input DTOs

### CreateSequenceData

| Field | Type | Validation |
|---|---|---|
| name | string | Required |
| owner_id | ulid? | Optional (null = team sequence) |
| trigger_type | string | In: manual, stage-change, segment-entry, deal-won, invoice-paid |
| trigger_config | array | Required for non-manual triggers |
| steps | array | `min:1`; each `{type, order, config, wait_days}` |

### EnrolData

| Field | Type | Validation |
|---|---|---|
| sequence_id | ulid | Required |
| contact_id | ulid | Not already actively enrolled — "Contact is already in this sequence." |
| deal_id | ulid? | Optional |

## Output DTOs

### EnrolmentData

Returned by `enrol()` — enrolment id, sequence_id, contact_id, deal_id, current_step, status, next_step_at, variant_map, enrolled_at.

### SequenceStatsData

Returned by `performance()` — reply rate, meetings booked, per-step counts (sent/opened/clicked/replied), per-variant breakdown.

## Public / Portal Endpoints

None. Sequences are internal CRM tooling with no public or portal-facing API.
