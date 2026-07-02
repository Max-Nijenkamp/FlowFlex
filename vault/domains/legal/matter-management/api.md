---
domain: legal
module: matter-management
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Matter Management — Service API

## DTOs

- `CreateMatterData` — title, type (in set), owner_id, priority/risk (in set), is_confidential + access_list.
- `AddMatterEventData` — matter_id, title, event_date, is_deadline, notes?.

## Methods

| Method | Purpose | Writes |
|---|---|---|
| `MatterService::create(CreateMatterData)` | new matter | `legal_matters` |
| `MatterService::accessibleFor(User): Builder` | confidentiality-scoped query | (read) |
| `MatterService::transition(id, state)` | status change | `legal_matters` |
| `AddMatterEventAction(AddMatterEventData)` | timeline event | `legal_matter_events` |

## Read surface (consumed by others)

- `legal.spend` reads matter list + `accessibleFor` scope to attach expenses (spend inherits matter confidentiality).

No events in v1.
