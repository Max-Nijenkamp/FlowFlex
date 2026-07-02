---
domain: support
module: automations
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Automations

Rule-based ticket automation: auto-assign, auto-tag, auto-escalate, and trigger actions on ticket events. Reduces manual triage.

---

## Module-key

`support.automations`

**Priority:** p2  
**Panel:** support  
**Permission prefix:** `support.automations`  
**Tables:** `sup_automation_rules`, `sup_automation_logs`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../tickets/_module\|support.tickets]] | rules operate on tickets |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../foundation/queue-workers/_module\|foundation.queues]] | gating, permissions, time-based runs |
| Soft | [[../sla/_module\|support.sla]] | SLA-warning trigger |
| Soft | [[../canned-responses/_module\|support.canned]] | send-canned-reply action |

---

## Core Features

- Automation rule: name, trigger event, conditions, actions, active toggle
- Triggers: ticket created, ticket updated, status changed, SLA warning, time-based (e.g. no reply in 24h)
- Conditions: match on category, priority, source, keyword in subject/body, requester attributes (AND logic, registry-validated *(assumed: AND only v1)*)
- Actions: assign to agent/team, set priority, add tag, send canned reply, escalate (priority bump + notify manager), notify
- Rule ordering: evaluated top to bottom; per-rule `stop_processing` flag
- Time-based rules run via a scheduled queue job
- Rule execution log: which rules fired on which tickets
- Loop guard: rule-driven updates don't re-trigger rule evaluation *(assumed: system-actor flag)*

See [[./features/automation-rules|Automation Rules]] and [[./features/time-based-rules|Time-Based Rules]] features.

---

## Build Manifest

```
database/migrations/xxxx_create_sup_automation_rules_table.php
database/migrations/xxxx_create_sup_automation_logs_table.php
app/Models/Support/{AutomationRule,AutomationLog}.php
app/Data/Support/CreateRuleData.php
app/Services/Support/AutomationEngine.php
app/Console/Commands/Support/{RunTimeBasedRulesCommand,PruneAutomationLogsCommand}.php
app/Filament/Support/Resources/AutomationRuleResource.php
database/factories/Support/AutomationRuleFactory.php
tests/Feature/Support/{AutomationEngineTest,TimeBasedRulesTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Rules evaluate in order; `stop_processing` halts chain
- [ ] Each condition operator fixture (keyword, category, priority)
- [ ] Each action type fixture (assign, priority, tag, canned reply, escalate)
- [ ] Rule-driven update doesn't re-trigger evaluation (loop guard)
- [ ] Time-based rule fires once per ticket
- [ ] Execution logged

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | ticket lifecycle hooks | support.tickets | same-domain direct calls on created/updated/status-changed |
| Reads | `RenderCannedResponseAction` | support.canned (soft) | send-canned-reply action |
| Reads | SLA warning signal | support.sla (soft) | SLA-warning trigger |
| Feeds | escalation notification | core.notifications | notify manager on escalate |

**Data ownership:** `support.automations` writes only `sup_automation_rules`, `sup_automation_logs`. Actions that mutate tickets call `TicketService` (Tickets owns those writes); it never writes `sup_tickets` directly ([[../../../security/data-ownership]]).

---

## Related

- [[../tickets/_module|support.tickets]]
- [[../sla/_module|support.sla]]
- [[../../../architecture/queue-jobs]]
