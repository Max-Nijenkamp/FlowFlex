---
domain: communications
module: automations
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Automations — API / DTOs

## DTOs

### `CreateAutomationRuleData` (input)

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `trigger` | enum | in: inbound-message, conversation-created, outside-hours |
| `channel_filter` | string nullable | channel type or null = all |
| `conditions` | array | registry-validated (AND) |
| `actions` | array | registry-validated typed configs |
| `order` | int | |
| `stop_processing` | boolean | |

### `CreateChatbotFlowData` (input)

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `channel` | string | required |
| `flow_definition` | jsonb | schema-validated: nodes reachable, no orphan refs, exit paths exist |

## Service surface (internal)

| Method | Kind | Notes |
|---|---|---|
| `CommsAutomationEngine::onInbound(Message): void` | command | invoked by `InboxService::handleInbound` |
| `ChatbotRunner::step(Conversation, input): ?string` | command | flow advance / handoff |

## Public / Portal Endpoints

None. Automations run in-process off the inbound pipeline.

## Related

- [[_module]] · [[architecture]] · [[../shared-inbox/api]]
