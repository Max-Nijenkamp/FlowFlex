---
type: module
domain: Support & Help Desk
panel: support
module-key: support.automations
status: planned
color: "#4ADE80"
---

# Automations

Rule-based ticket automation: auto-assign, auto-tag, auto-escalate, and trigger actions on ticket events. Reduces manual triage.

## Core Features

- Automation rule: name, trigger event, conditions, actions, active toggle
- Triggers: ticket created, ticket updated, status changed, SLA warning, time-based (e.g. no reply in 24h)
- Conditions: match on category, priority, source, keyword in subject/body, requester attributes
- Actions: assign to agent/team, set priority, add tag, send canned reply, escalate, notify
- Rule ordering: rules evaluated top to bottom, first match can stop or continue
- Time-based rules run via scheduled queue job
- Rule execution log: which rules fired on which tickets

## Data Model

| Table | Key Columns |
|---|---|
| `sup_automation_rules` | company_id, name, trigger_event, conditions (json), actions (json), order, is_active |
| `sup_automation_logs` | company_id, rule_id, ticket_id, executed_at, result |

## Filament

**Nav group:** Settings

- `AutomationRuleResource` — list, create, edit (condition + action builder), reorder
- Rule builder uses Filament repeater for conditions and actions

## Cross-Domain / Jobs

- Event-driven rules hook into ticket lifecycle events
- Time-based rules run via scheduled job (see [[architecture/queue-jobs]])

## Related

- [[domains/support/tickets]]
- [[domains/support/sla]]
- [[architecture/event-bus]]
