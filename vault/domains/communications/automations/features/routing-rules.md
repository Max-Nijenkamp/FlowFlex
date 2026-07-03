---
domain: communications
module: automations
feature: routing-rules
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

## Test Checklist

### Unit
- [ ] Ordered evaluation: rules execute in `order`; `stop_processing = true` halts remaining rules on match
- [ ] Action config validates against the action registry (assign/tag/set-status/escalate)

### Feature (Pest)
- [ ] Inbound matching a routing rule assigns/tags/sets-status via `InboxService` (inbox owns the writes)
- [ ] Stop-on-match: a later rule does not run once an earlier match sets `stop_processing`
- [ ] Tenant isolation: a rule never routes a conversation from another company

### Livewire
- [ ] Drag-reorder persists `order`; stop-on-match toggle saves
- [ ] Create/edit denied without `comms.automations.manage`

## Related

- [[../_module|Automations]] · [[auto-reply-rules]] · [[chatbot-flows]]
