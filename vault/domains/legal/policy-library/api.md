---
domain: legal
module: policy-library
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Policy Library — Service API

## DTOs

- `CreatePolicyData` — title, category, body (purified), effective_date, review_date?, audience?.
- `AcknowledgeData` — policy_id (published, in audience) — actor's own employee record.

## Methods

| Method | Purpose | Writes |
|---|---|---|
| `PolicyService::create(CreatePolicyData)` | draft policy | `legal_policies` |
| `PolicyService::publish(id)` | version bump on body change, reset acks, notify audience | `legal_policies`, `legal_policy_acknowledgements` (reset) |
| `AcknowledgePolicyAction(AcknowledgeData)` | acknowledge own | `legal_policy_acknowledgements` |

## Read surface (consumed by others)

- `legal.compliance` reads acknowledgement status as control evidence.

No events fired in v1; audience resolved by reading `hr.profiles` departments/employees.
