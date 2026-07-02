---
domain: support
module: sla
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# SLA Management — DTOs & API

## DTOs

### CreateSlaPolicyData (input)

| Field | Type | Validation |
|---|---|---|
| name | string | required |
| business_hours_only | bool | |
| targets | array | one row per priority |
| targets[].priority | string | in set, unique in policy |
| targets[].first_response_minutes | int | min:1 |
| targets[].resolution_minutes | int | > first_response_minutes |

### SlaComplianceData (output)

Period, per-target compliance %, met vs breached counts, breakdown by priority.

---

## Public / Portal Endpoints

None. All access is via the `/support` Filament panel (`support` guard). Timer checks run as a scheduled command, not an endpoint.
