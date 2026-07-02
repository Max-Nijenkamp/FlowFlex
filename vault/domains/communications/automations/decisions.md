---
domain: communications
module: automations
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Automations — Decisions

## ADR: Engine invoked in-process by the inbox (source)

- **Context:** Automations must react to inbound messages.
- **Decision:** `InboxService::handleInbound` calls `CommsAutomationEngine::onInbound` directly (in-process), rather than the inbox firing a bus event. Chatbot runs first, then ordered rules.
- **Consequences:** Tight coupling to the inbox (hard dep), but no event contract needed. See [[unknowns]] for whether this should become an event.

## ADR: All side effects go through `InboxService` (data-ownership)

- **Decision:** Rule actions (assign, tag, set-status, auto-reply) call `InboxService`; automations never write `comms_conversations` / `comms_messages`.
- **Consequences:** Inbox tables stay single-owner ([[../../../security/data-ownership]]).

## ADR: Loop guard via system actor (source)

- **Decision:** Automation-sent replies are stamped with a system actor and excluded from `onInbound` re-entry.
- **Consequences:** No infinite auto-reply loops.

## ADR: No separate execution-log table (source, assumed)

- **Decision:** Use in-rule `run_count` counters + `spatie/laravel-activitylog` instead of a dedicated log table *(assumed)*.

## Related

- [[_module]] · [[architecture]]
