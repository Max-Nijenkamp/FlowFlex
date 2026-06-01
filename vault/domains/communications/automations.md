---
type: module
domain: Communications
panel: comms
module-key: comms.automations
status: planned
color: "#4ADE80"
---

# Automations

Auto-reply rules, conversation routing, and chatbot flows for the shared inbox. Reduces manual triage of incoming messages.

## Core Features

- Auto-reply rules: trigger on inbound message matching conditions
- Routing rules: assign conversation to agent/team by channel, keyword, or time
- Conditions: channel, keyword in message, time of day, contact attributes, business hours
- Actions: auto-reply with template, assign, tag, set status, escalate
- Away messages: auto-reply outside business hours
- Simple chatbot flows: keyword-triggered decision trees (e.g. "1 for sales, 2 for support")
- Rule ordering and stop-on-match
- Execution log

## Data Model

| Table | Key Columns |
|---|---|
| `comms_automation_rules` | company_id, name, channel_filter, trigger, conditions (json), actions (json), order, is_active |
| `comms_chatbot_flows` | company_id, name, channel, flow_definition (json), is_active |

## Filament

**Nav group:** Settings

- `CommsAutomationRuleResource` — list, create, edit rules
- `ChatbotFlowResource` — build keyword decision trees

## Cross-Domain

- Hooks into inbound message events (see [[architecture/event-bus]])

## Related

- [[domains/communications/shared-inbox]]
- [[domains/support/automations]] — similar pattern
