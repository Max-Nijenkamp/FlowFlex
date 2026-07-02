---
domain: support
module: automations
feature: automation-rules
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Automation Rules

Build event-triggered rules (conditions → actions) that fire on ticket lifecycle events.

## Behaviour

- Rule: name, trigger event (created/updated/status-changed/sla-warning), conditions (AND, registry-validated), actions, `order`, `stop_processing`, `is_active`.
- On a ticket event, `AutomationEngine::evaluate` runs active rules for that trigger in order; matching rules execute actions and log; `stop_processing` halts the chain.
- Actions: assign, set priority, add tag, send canned reply (soft-dep), escalate (priority bump + notify manager), notify — all via `TicketService` / notification service.
- Loop guard prevents rule-driven updates from re-triggering evaluation.

## UI

- **Kind**: simple-resource — `AutomationRuleResource` CRUD with condition/action repeaters + a logs relation manager.
- **Page**: `AutomationRuleResource` (`/support/automations`).
- **Layout**: reorderable list (name, trigger badge, active toggle); form = trigger select + conditions repeater + actions repeater + stop-processing + test-run preview *(assumed)*; rule view has a logs tab.
- **Key interactions**: drag to reorder; add condition/action rows (registry-driven selects); toggle active; test-run preview against a sample ticket.
- **States**: empty (no rules → "create your first rule" CTA) · loading (save) · error (invalid field/operator/action rejected) · selected (editing a rule, logs tab).
- **Gating**: view `support.automations.view-any`; edit `support.automations.manage`.

## Data

- Owns / writes: `sup_automation_rules`, `sup_automation_logs`.
- Reads: ticket fields (evaluation); canned responses / SLA signals (soft).
- Cross-domain writes: none — ticket mutations go through `TicketService` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: ticket lifecycle events (same-domain), SLA warning (soft), canned responses (soft).
- Feeds: escalation notifications → core.notifications.
- Shared entity: `sup_tickets` (read + mutate via service).

## Unknowns

- AND-only logic; loop-guard mechanism *(assumed)* — [[../unknowns]].

## Related

- [[../_module|Automations]] · [[./time-based-rules]] · [[../../tickets/_module|support.tickets]]
