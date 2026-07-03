---
domain: communications
module: automations
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Automations

Auto-reply rules, conversation routing, and keyword chatbot flows for the shared inbox. Reduces manual triage of incoming messages.

> Hooks into the inbox inbound pipeline. Owns its rule + flow tables; acts on inbox conversations via the inbox service (never writes inbox tables directly).

## Module-key

`comms.automations`

**Priority:** p2  
**Panel:** comms  
**Permission prefix:** `comms.automations`  
**Tables:** `comms_automation_rules`, `comms_chatbot_flows`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../shared-inbox/_module\|comms.inbox]] | hooks into the inbound pipeline |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Hard | [[../../core/company-settings/_module\|core.settings]] | business hours for away messages |

## Core Features

- Auto-reply rules: trigger on inbound message matching conditions.
- Routing rules: assign a conversation to an agent/team by channel, keyword, or time.
- Conditions: channel, keyword-in-message, time of day, contact attributes, business hours (AND logic *(assumed)*).
- Actions: auto-reply with template, assign, tag, set status, escalate.
- Away messages: auto-reply outside business hours (once per conversation per day *(assumed)*).
- Simple chatbot flows: keyword-triggered decision trees ("1 for sales, 2 for support") — flow state per conversation, exits to human on no-match.
- Rule ordering + stop-on-match.
- Execution log (in-rule jsonb counters + activitylog *(assumed)*).
- Loop guard: automation-sent replies never re-trigger rules.

## See features/

- [[features/auto-reply-rules|Auto-reply Rules]] — condition/action rules on inbound; away messages.
- [[features/routing-rules|Routing Rules]] — assign/tag/status by channel/keyword/time; ordering + stop-on-match.
- [[features/chatbot-flows|Chatbot Flows]] — keyword decision trees with human-handoff on no-match.

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

## Test Checklist

- [ ] Tenant isolation: company A rules never evaluate against company B conversations; engine runs under the conversation's company context.
- [ ] Module gating: artifacts hidden when `comms.automations` inactive.
- [ ] Rules run in order; stop-on-match halts.
- [ ] Away message only outside business hours, once per conversation/day.
- [ ] Auto-reply doesn't re-trigger the engine (loop guard).
- [ ] Chatbot: option match advances; double no-match exits to human.
- [ ] Flow-definition validation rejects orphan nodes.
- [ ] Each action-type fixture.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | inbound hook | [[../shared-inbox/_module\|comms.inbox]] | `CommsAutomationEngine::onInbound` called by `InboxService::handleInbound` |
| Acts via | `InboxService` (assign/tag/status/reply) | [[../shared-inbox/_module\|comms.inbox]] | mutates conversations through the inbox service, not by writing inbox tables |
| Reads | business hours | [[../../core/company-settings/_module\|core.settings]] | away-message window |

No cross-domain **domain events** fired or consumed (see [[../../../architecture/event-bus]]).

**Data ownership:** `comms.automations` writes **only** `comms_automation_rules` and `comms_chatbot_flows`. All conversation effects (assign, tag, set-status, auto-reply) go **through `InboxService`**, which writes the inbox-owned tables — automations never write `comms_conversations` / `comms_messages` directly ([[../../../security/data-ownership]]).

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../shared-inbox/_module|Shared Inbox]] · [[../../support/automations/_module|Support Automations]] (same pattern, tickets)
