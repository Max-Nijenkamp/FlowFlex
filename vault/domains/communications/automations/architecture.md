---
domain: communications
module: automations
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `CommsAutomationRuleResource` | #1 CRUD resource | tweaks: inline-relation-repeater (condition/action repeaters), custom-header-actions (activate/deactivate) *(assumed)* | drag-reorder for rule `order`, stop-on-match toggle; shared by auto-reply + routing rules |
| `ChatbotFlowResource` | #1 CRUD resource | tweaks: inline-relation-repeater (node/option builder) | node repeater (tree); flow-definition validation rejects orphan nodes |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('comms.automations.view-any') && BillingService::hasModule('comms.automations')`
per [[../../../architecture/filament-patterns]] #1. Both are standard resources (no custom page); Filament auto-gates
resources but the pair are settings-only and additionally require `comms.automations.manage` for write actions
([[./security]]). Rules act on conversations only through `InboxService` — no artifact writes inbox-owned tables.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Automation-rule CRUD (form, reorder, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Chatbot-flow CRUD (form, node edits) | Optimistic | `updated_at` stale-check ([[../../../architecture/patterns/optimistic-locking]]) |
| Rule execution (assign / tag / status / auto-reply) | n/a | No write to this module's tables — all conversation effects go through `InboxService`, which owns those write paths and their concurrency tier |
| Chatbot flow-position advance | n/a | Position lives in inbox-owned `comms_conversations` meta; written via `InboxService`, not here |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

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
