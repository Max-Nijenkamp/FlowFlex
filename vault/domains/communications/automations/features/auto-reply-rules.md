---
domain: communications
module: automations
feature: auto-reply-rules
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Auto-reply Rules

Rules that send a templated reply on inbound messages matching conditions — including away messages outside business hours.

## Behaviour

- Trigger on inbound-message / conversation-created / outside-hours.
- Conditions (AND *(assumed)*): channel, keyword, time of day, contact attributes, business hours.
- Action: auto-reply with a template (sent via `InboxService`).
- Away message: outside business hours (from `core.settings`), once per conversation per day *(assumed)*.
- Loop guard: the auto-reply itself never re-triggers the engine.

## UI

- **Kind**: simple-resource (`CommsAutomationRuleResource`, shared with routing rules — Settings nav group).
- **Layout**: table (name, trigger, active, order) + form (condition repeater, action = auto-reply template).
- **Key interactions**: build conditions → pick reply template → activate; reorder among all rules.
- **States**: empty (no rules → CTA) · loading · error (invalid condition/action registry ref) · selected.
- **Gating**: `comms.automations.manage`.

## Data

- Owns / writes: `comms_automation_rules` (own module).
- Reads: `core.settings` business hours (read-only).
- Cross-domain writes: none — the reply is written by `InboxService` (inbox-owned), not this module ([[../../../security/data-ownership]]).

## Relations

- Consumes: inbound hook from [[../../shared-inbox/_module|comms.inbox]]; business hours from [[../../core/company-settings/_module|core.settings]].
- Feeds: auto-reply message via `InboxService`.
- Shared entity: `comms_messages` (owned by the inbox).

## Related

- [[../_module|Automations]] · [[routing-rules]] · [[chatbot-flows]]
