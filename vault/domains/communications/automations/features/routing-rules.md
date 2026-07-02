---
domain: communications
module: automations
feature: routing-rules
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Routing Rules

Rules that assign, tag, set status, or escalate a conversation by channel, keyword, or time — ordered, with stop-on-match.

## Behaviour

- Same rule engine as auto-reply, but actions are: assign (agent/team), tag, set status, escalate.
- Rules run in `order`; `stop_processing = true` halts remaining rules on a match.
- All actions execute through `InboxService` (assign/tag/setStatus).

## UI

- **Kind**: simple-resource (`CommsAutomationRuleResource` — Settings nav group; shared with auto-reply rules).
- **Layout**: table with drag-reorder + stop-on-match toggle; form with condition + action repeaters.
- **Key interactions**: build conditions → pick action(s) → order rules → toggle stop-on-match.
- **States**: empty · loading · error (invalid registry ref) · selected (reorder handle active).
- **Gating**: `comms.automations.manage`.

## Data

- Owns / writes: `comms_automation_rules` (own module).
- Reads: agents/teams for assignment (RBAC), channels — read-only.
- Cross-domain writes: none — assign/tag/status changes go through `InboxService` (inbox-owned) ([[../../../security/data-ownership]]).

## Relations

- Consumes: inbound hook from [[../../shared-inbox/_module|comms.inbox]].
- Feeds: conversation mutations via `InboxService::assign / setStatus` + tags.
- Shared entity: `comms_conversations` (owned by the inbox); users/teams (RBAC).

## Related

- [[../_module|Automations]] · [[auto-reply-rules]] · [[chatbot-flows]]
