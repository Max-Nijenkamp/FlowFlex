---
domain: support
module: automations
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Automations — Architecture

## Services & Actions

- `AutomationEngine::evaluate(Ticket $ticket, string $trigger): void` — loads ordered active rules for the trigger, matches conditions (AND), executes actions, logs; system-actor guard against loops
- Hooked into the ticket lifecycle (created / updated / status transitions) via same-domain direct calls
- `RunTimeBasedRulesCommand` — finds tickets matching each rule's `time_config`
- Actions never write `sup_tickets` directly — assign/set-priority/tag/escalate call `TicketService`; the owning domain performs the write ([[../../../security/data-ownership]])

Conditions + actions are registry-validated (known fields/operators/action types) *(assumed: AND-only in v1)*.

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RunTimeBasedRulesCommand` | default | every 15 min | unique `(rule, ticket)` log row within window — won't double-fire *(assumed: once per rule per ticket)* |
| `PruneAutomationLogsCommand` | default | daily | date guard; prunes logs > 90 days *(assumed)* |

---

## Filament Artifacts

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `AutomationRuleResource` | #1 CRUD resource | condition + action repeaters, reorder, test-run preview *(assumed)* |
| Logs relation manager | on rule view | execution history |

**Access contract:** gates on `canAccess() = Auth::user()->can('support.automations.view-any') && BillingService::hasModule('support.automations')` per [[../../../architecture/filament-patterns]] #1.

---

## Search & Realtime

No search, no realtime. Rules evaluated synchronously on ticket events + a scheduled command for time-based triggers.

See [[./security]] for permissions + the loop-guard rationale.
