---
domain: communications
module: automations
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Automations — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `CommsAutomationEngine::onInbound` | `onInbound(Message): void` | Called by `InboxService::handleInbound`. Runs the active chatbot flow first (if the conversation is in a flow), then ordered rules. System-actor loop guard prevents automation-sent replies from re-triggering. |
| `ChatbotRunner::step` | `step(Conversation, string $input): ?string` | Matches the input to a flow option → reply/advance; two consecutive no-matches → exit to human + route rule. |

Actions available to rules: auto-reply (template), assign, tag, set status, escalate — all executed **through `InboxService`** so the inbox owns the writes.

## Events

None fired or consumed. The engine is invoked by the inbox's inbound handler (in-process call), not via the event bus. See [[../../../architecture/event-bus]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `CommsAutomationRuleResource` | Settings | #1 CRUD resource | condition/action repeaters, drag-reorder, stop-on-match toggle. |
| `ChatbotFlowResource` | Settings | #1 CRUD resource | node repeater builder (tree); flow-definition validation. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.automations.view-any')
        && BillingService::hasModule('comms.automations');
}
```

## Rule Engine

- Conditions are AND-combined *(assumed)*, validated against a condition registry.
- Actions are typed configs validated against an action registry.
- Rules run in `order`; `stop_processing = true` halts remaining rules on match.
- Chatbot flow state (position) held in `comms_conversations` meta *(assumed jsonb)*.

## Loop Guard

Automation-generated replies are stamped with a system actor and **do not** re-enter `onInbound`, preventing infinite auto-reply loops.

## Implementation Notes (tense-softened)

- The engine is designed to run **chatbot-first, then rules**, so an active flow owns the conversation until it exits.
- Away messages are designed to fire **once per conversation per day** *(assumed)* and only outside business hours (from `core.settings`).
- All side effects are designed to route **through `InboxService`**, keeping inbox tables single-owner.

## Related

- [[_module]] · [[data-model]] · [[../shared-inbox/_module|Shared Inbox]]
