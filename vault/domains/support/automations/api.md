---
domain: support
module: automations
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Automations — DTOs & API

## DTOs

### CreateRuleData (input)

| Field | Type | Validation |
|---|---|---|
| name | string | required |
| trigger_event | string | in set (created/updated/status-changed/sla-warning/time-based) |
| conditions | array | `[{field, operator, value}]` — fields/operators registry-validated (AND) |
| actions | array | `[{type, config}]` — types registry-validated, config per type |
| time_config | ?array | `{after_minutes, when}` (required when trigger = time-based) |
| order | int | evaluation order |
| stop_processing | bool | halt chain after this rule |

---

## Public / Portal Endpoints

None. Automations run internally on ticket events + scheduled commands; managed only via the `/support` Filament panel (`support` guard).
