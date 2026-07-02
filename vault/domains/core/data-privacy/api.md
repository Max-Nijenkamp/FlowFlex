---
domain: core
module: data-privacy
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Data Privacy — API (DTO, Event, Contract)

Parent: [[_module]] · See also [[architecture]]

## Contract

`PersonalDataRegistry` is the cross-module surface: every module registers its PII tables/fields in its own ServiceProvider so export and erasure stay in sync as domains ship.

| Method | Behavior |
|---|---|
| `register(string $moduleKey, array $tablesFields)` | declare a module's PII tables/columns |
| `tablesFor(string $email)` | resolve tables/rows for a data subject |

## DTOs

### CreateDsarRequestData (input)

| Field | Type | Validation |
|---|---|---|
| subject_email | string | required, email |
| request_type | string | required, `in:access,erasure` |

## Events fired

### DSARRequestSubmitted

Fired on create (not on state transition).

| Field | Type |
|---|---|
| company_id | string |
| dsar_request_id | string |
| request_type | string |
| subject_email | string |
| due_at | CarbonImmutable |

Consumers per [[../../../architecture/data-lifecycle]] and the event bus (Notifications now; Legal in P3). Consumes no events.
