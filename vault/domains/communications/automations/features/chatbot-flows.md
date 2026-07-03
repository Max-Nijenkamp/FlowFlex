---
domain: communications
module: automations
feature: chatbot-flows
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Chatbot Flows

Keyword-triggered decision trees ("1 for sales, 2 for support") that hand off to a human on no-match.

## Behaviour

- A flow is a tree of nodes: `{id, message, options: [{match, next/action}]}`.
- On a matching inbound, `ChatbotRunner::step` replies with the node message and advances on the customer's option choice.
- Flow state (current node) is held per conversation in `comms_conversations` meta *(assumed)*.
- Two consecutive no-matches → exit to human + trigger the routing rule.
- Flow definition validated: nodes reachable, no orphan refs, exit paths exist.

## UI

- **Kind**: simple-resource (`ChatbotFlowResource`, node repeater/tree editor — Settings nav group). *(If a drag-and-drop node graph is needed later, promote to a custom-page.)*
- **Layout**: table (name, channel, active) + form with a node repeater (message + options builder).
- **Key interactions**: add nodes → wire options to next/action → validate → activate (one active per channel *(assumed)*).
- **States**: empty (no flows → CTA) · loading · error (validation rejects orphan node / no exit) · selected.
- **Gating**: `comms.automations.manage`.

## Data

- Owns / writes: `comms_chatbot_flows` (own module).
- Reads: flow position from conversation meta (inbox-owned) — read; write of position goes through `InboxService` *(assumed)*.
- Cross-domain writes: none — replies + handoff routing go through `InboxService` ([[../../../security/data-ownership]]).

## Relations

- Consumes: inbound hook from [[../../shared-inbox/_module|comms.inbox]].
- Feeds: chatbot replies + human-handoff routing via `InboxService`.
- Shared entity: `comms_conversations` (owned by the inbox; holds flow position).

## Test Checklist

### Unit
- [ ] Flow-definition validation rejects orphan nodes / missing exit paths
- [ ] `ChatbotRunner::step` advances to the `next` node on a matching option

### Feature (Pest)
- [ ] Matching option advances the flow; position persisted to conversation meta via `InboxService`
- [ ] Two consecutive no-matches exit to human and trigger the routing rule
- [ ] Reply + handoff routing go through `InboxService` (no direct inbox-table write)

### Livewire
- [ ] Node repeater builds the tree; validation blocks activation of an orphaned flow
- [ ] Create/edit denied without `comms.automations.manage`

## Related

- [[../_module|Automations]] · [[auto-reply-rules]] · [[../unknowns]]
