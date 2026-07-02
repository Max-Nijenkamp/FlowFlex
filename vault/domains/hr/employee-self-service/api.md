---
domain: hr
module: employee-self-service
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# API — Employee Self-Service

> Intended DTO/service contracts. No events (`fires-events: []`, `consumes-events: []`). Nothing built.

## DTOs

### `UpdateOwnProfileData` (input)

| Field | Type | Validation |
|---|---|---|
| phone | ?string | nullable, phone:AUTO |
| personal_email | ?string | nullable, email |
| emergency_contacts | array<{name, relationship, phone, email?}> | max 3 *(assumed — see [[unknowns]])* |

Employees may **NOT** edit: `name`, `email`, `job`, `salary`, `department`, `manager`, `national_id` — HR-only fields (rejected server-side).

## Actions

- `UpdateOwnProfileAction::run(UpdateOwnProfileData $data): void` — operates strictly on `auth()->user()->employee`. See [[architecture]] for the own-data rule.

## Related

- [[_module]] · [[security]]
