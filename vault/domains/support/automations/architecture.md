---
domain: support
module: automations
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `AutomationRuleResource` | #1 CRUD resource | tweaks: inline-relation-repeater (conditions + actions), view-page-tabs (logs tab), custom-header-actions (test-run preview *(assumed)*) | reorderable rule list; trigger + active-toggle columns |
| Logs relation manager | #1 relation manager (on rule view) | read-only append-only history | `sup_automation_logs` — which rules fired on which tickets |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('support.automations.view-any') && BillingService::hasModule('support.automations')`
per [[../../../architecture/filament-patterns]] #1. This is a backend-heavy module — the rule engine runs synchronously on ticket events plus a scheduled command; there is no custom page to auto-gate.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Rule CRUD + reorder (form) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Execution log append (engine / scheduled run) | n/a | append-only insert into `sup_automation_logs`; the unique `(rule, ticket)` window row is the fire-once idempotency guard, not a lock |
| Ticket mutation from an action | Pessimistic | delegated to `TicketService` — the owning domain performs the locked write per [[../../../architecture/patterns/states]] ([[../tickets/architecture]]) |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

No search, no realtime. Rules evaluated synchronously on ticket events + a scheduled command for time-based triggers.

See [[./security]] for permissions + the loop-guard rationale.
