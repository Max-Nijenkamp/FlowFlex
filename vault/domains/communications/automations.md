---
type: module
domain: Communications
domain-key: communications
panel: comms
module-key: comms.automations
status: planned
priority: p2
depends-on: [comms.inbox, core.billing, core.rbac, core.settings]
soft-depends: []
fires-events: []
consumes-events: []
patterns: []
tables: [comms_automation_rules, comms_chatbot_flows]
permission-prefix: comms.automations
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Automations

Auto-reply rules, conversation routing, and chatbot flows for the shared inbox. Reduces manual triage of incoming messages.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/communications/shared-inbox\|comms.inbox]] | hooks into inbound pipeline |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/company-settings\|core.settings]] | gating, permissions, business hours |

---

## Core Features

- Auto-reply rules: trigger on inbound message matching conditions
- Routing rules: assign conversation to agent/team by channel, keyword, or time
- Conditions: channel, keyword in message, time of day, contact attributes, business hours (AND logic *(assumed)*)
- Actions: auto-reply with template, assign, tag, set status, escalate
- Away messages: auto-reply outside business hours (once per conversation per day *(assumed)*)
- Simple chatbot flows: keyword-triggered decision trees (e.g. "1 for sales, 2 for support") — flow state held per conversation, exits to human on no-match
- Rule ordering and stop-on-match
- Execution log (in-rule jsonb counters + activitylog *(assumed: no separate log table — counters + audit)*)
- Loop guard: automation-sent replies never re-trigger rules

---

## Data Model

### comms_automation_rules

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| channel_filter | string nullable | channel type or null=all |
| trigger | string | inbound-message / conversation-created / outside-hours |
| conditions | jsonb | AND rules, registry-validated |
| actions | jsonb | typed action configs |
| order | int | |
| stop_processing | boolean default false | |
| is_active | boolean default true | |
| run_count | int default 0 | |
| deleted_at | timestamp nullable | |

### comms_chatbot_flows

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| channel | string | |
| flow_definition | jsonb | nodes: {id, message, options: [{match, next/action}]} |
| is_active | boolean | one active flow per channel *(assumed)* |

Conversation flow position: `comms_conversations` meta *(assumed: jsonb meta column)*.

---

## DTOs

### CreateAutomationRuleData — name, trigger (in set), channel_filter?, conditions[] (registry), actions[] (registry), order, stop_processing
### CreateChatbotFlowData — name, channel, flow_definition (schema-validated: nodes reachable, no orphan refs, exit paths exist)

## Services & Actions

- `CommsAutomationEngine::onInbound(Message $message): void` — called by `InboxService::handleInbound`; chatbot flow first (if active + conversation in flow), then ordered rules; system-actor loop guard
- `ChatbotRunner::step(Conversation $c, string $input): ?string` — match option → reply/advance; no match twice → exit to human + route rule

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CommsAutomationRuleResource` | #1 CRUD resource | condition/action repeaters, reorder |
| `ChatbotFlowResource` | #1 CRUD resource | node repeater builder (tree) |

---

## Permissions

`comms.automations.view-any` · `comms.automations.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Rules in order; stop-on-match halts
- [ ] Away message only outside business hours, once per conversation/day
- [ ] Auto-reply doesn't re-trigger engine (loop guard)
- [ ] Chatbot: option match advances; double no-match exits to human
- [ ] Flow definition validation rejects orphan nodes
- [ ] Each action type fixture

---

## Build Manifest

```
database/migrations/xxxx_create_comms_automation_rules_table.php
database/migrations/xxxx_create_comms_chatbot_flows_table.php
app/Models/Comms/{CommsAutomationRule,ChatbotFlow}.php
app/Data/Comms/{CreateAutomationRuleData,CreateChatbotFlowData}.php
app/Services/Comms/{CommsAutomationEngine,ChatbotRunner}.php
app/Filament/Comms/Resources/{CommsAutomationRuleResource,ChatbotFlowResource}.php
database/factories/Comms/CommsAutomationRuleFactory.php
tests/Feature/Comms/{CommsAutomationTest,ChatbotFlowTest}.php
```

---

## Related

- [[domains/communications/shared-inbox]]
- [[domains/support/automations]] — same pattern, tickets
