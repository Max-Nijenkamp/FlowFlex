---
type: module
domain: Support & Help Desk
panel: support
module-key: support.sla
status: planned
color: "#4ADE80"
---

# SLA Management

Service Level Agreement policies: first-response and resolution time targets per priority. Tracks compliance and alerts before breach.

## Core Features

- SLA policy: name, per-priority targets (first response time, resolution time)
- Business hours: targets count only during configured business hours (per company timezone)
- SLA timer per ticket: counts up from ticket creation; pauses on `waiting_on_customer`
- Breach detection: ticket exceeds target → flag + notification to assignee and manager
- Breach warning: notify when 80% of SLA time elapsed
- SLA assignment: by ticket category or priority
- SLA compliance report: % of tickets meeting first-response and resolution targets
- Pause/resume logic: clock pauses when ticket is waiting on customer

## Data Model

| Table | Key Columns |
|---|---|
| `sup_sla_policies` | company_id, name, business_hours_only |
| `sup_sla_targets` | sla_policy_id, company_id, priority, first_response_minutes, resolution_minutes |
| `sup_sla_events` | company_id, ticket_id, type (first_response_met/breached, resolution_met/breached), occurred_at |

## Filament

**Nav group:** Settings

- `SlaPolicyResource` — list, create, edit policies + per-priority targets
- `SlaMonitorPage` (custom page) — live view of tickets approaching breach (updates via Reverb WebSocket)
- `SlaComplianceWidget` — % compliance dashboard widget

## Cross-Domain / Jobs

- Scheduled job checks SLA timers, fires breach warnings and breach events
- Uses queue (see [[architecture/queue-jobs]])

## Related

- [[domains/support/tickets]]
- [[architecture/websockets]]
