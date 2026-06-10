---
type: module
domain: Support & Help Desk
domain-key: support
panel: support
module-key: support.automations
status: planned
priority: p2
depends-on: [support.tickets, core.billing, core.rbac, foundation.queues]
soft-depends: [support.sla, support.canned]
fires-events: []
consumes-events: []
patterns: [queues]
tables: [sup_automation_rules, sup_automation_logs]
permission-prefix: support.automations
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Automations

Rule-based ticket automation: auto-assign, auto-tag, auto-escalate, and trigger actions on ticket events. Reduces manual triage.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/support/tickets\|support.tickets]] | rules operate on tickets |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, time-based runs |
| Soft | [[domains/support/sla\|support.sla]] | SLA-warning trigger |
| Soft | [[domains/support/canned-responses\|support.canned]] | send-canned-reply action |

---

## Core Features

- Automation rule: name, trigger event, conditions, actions, active toggle
- Triggers: ticket created, ticket updated, status changed, SLA warning, time-based (e.g. no reply in 24h)
- Conditions: match on category, priority, source, keyword in subject/body, requester attributes (AND logic, registry-validated *(assumed: AND only v1)*)
- Actions: assign to agent/team, set priority, add tag, send canned reply, escalate (priority bump + notify manager), notify
- Rule ordering: rules evaluated top to bottom; per-rule `stop_processing` flag
- Time-based rules run via scheduled queue job
- Rule execution log: which rules fired on which tickets
- Loop guard: rule-driven updates don't re-trigger rule evaluation *(assumed: system-actor flag)*

---

## Data Model

### sup_automation_rules

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| trigger_event | string | created / updated / status-changed / sla-warning / time-based |
| conditions | jsonb | [{field, operator, value}] AND |
| actions | jsonb | [{type, config}] |
| time_config | jsonb nullable | {after_minutes, when: no-reply} |
| order | int | evaluation order |
| stop_processing | boolean default false | |
| is_active | boolean default true | |
| deleted_at | timestamp nullable | |

### sup_automation_logs — id, company_id (indexed), rule_id FK, ticket_id FK, executed_at, result (jsonb); pruned 90 days *(assumed)*

---

## DTOs

### CreateRuleData — name, trigger_event (in set), conditions[] (fields/operators registry-validated), actions[] (types registry-validated, configs per type), order, stop_processing

## Services & Actions

- `AutomationEngine::evaluate(Ticket $ticket, string $trigger): void` — ordered active rules for trigger, condition match → execute actions, log; system-actor guard against loops
- Hooked into ticket lifecycle (created/updated/status transitions — direct same-domain calls)
- `RunTimeBasedRulesCommand` — finds matching tickets per time_config

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RunTimeBasedRulesCommand` | default | every 15 min | unique `(rule, ticket)` log row within window — won't double-fire *(assumed: once per rule per ticket)* |
| `PruneAutomationLogsCommand` | default | daily | date guard |

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `AutomationRuleResource` | #1 CRUD resource | condition + action repeaters, reorder, test-run preview *(assumed)* |
| Logs relation manager | on rule view | execution history |

---

## Permissions

`support.automations.view-any` · `support.automations.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Rules evaluate in order; stop_processing halts chain
- [ ] Each condition operator fixture (keyword, category, priority)
- [ ] Each action type fixture (assign, priority, tag, canned reply, escalate)
- [ ] Rule-driven update doesn't re-trigger evaluation (loop guard)
- [ ] Time-based rule fires once per ticket
- [ ] Execution logged

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

## Related

- [[domains/support/tickets]]
- [[domains/support/sla]]
- [[architecture/queue-jobs]]
