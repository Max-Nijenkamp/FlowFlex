---
domain: support
module: automations
feature: time-based-rules
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Time-Based Rules

Rules triggered by elapsed time rather than an event (e.g. "no reply in 24h → escalate").

## Behaviour

- Trigger `time-based` with `time_config` `{after_minutes, when: no-reply}`.
- `RunTimeBasedRulesCommand` sweeps every 15 min, finds tickets matching each rule's `time_config` + conditions, executes actions, logs.
- Idempotency: unique `(rule, ticket)` log row within the window — a rule fires at most once per ticket *(assumed)*.

## UI

- **Kind**: background — no dedicated page; configured through the same `AutomationRuleResource` (trigger = time-based reveals a `time_config` sub-form). Runs as a scheduled command.
- **Trigger**: `RunTimeBasedRulesCommand` (every 15 min).

## Data

- Owns / writes: `sup_automation_rules` (config), `sup_automation_logs` (executions).
- Reads: `sup_tickets` timestamps/status to find matches.
- Cross-domain writes: none — actions via `TicketService` / notifications ([[../../../../security/data-ownership]]).

## Relations

- Consumes: scheduled sweep (foundation.queues).
- Feeds: escalation notifications → core.notifications.
- Shared entity: `sup_tickets` (read + mutate via service).

## Unknowns

- Fire-once-per-ticket semantics + window definition *(assumed)* — [[../unknowns]].

## Related

- [[../_module|Automations]] · [[./automation-rules]] · [[../../../../architecture/queue-jobs]]
