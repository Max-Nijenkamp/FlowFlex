---
domain: support
module: automations
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Automations — Local Decisions

## Decided

- **Engine mutates tickets via TicketService, not direct writes.** Preserves data-ownership: Tickets owns `sup_tickets`; Automations owns only rules + logs ([[../../../security/data-ownership]]).
- **jsonb conditions/actions with a validation registry.** Flexible rule shape without a schema explosion; a registry of allowed fields/operators/action-types keeps it safe + typed. AND-only logic in v1 *(assumed)*.
- **System-actor loop guard.** Rule-driven updates carry a flag so they don't re-trigger evaluation — the single biggest automation footgun.
- **Time-based rules are a scheduled command, not per-ticket cron.** One 15-min sweep with `(rule, ticket)` idempotency.

## Assumed (overridable via ADR)

- AND-only condition logic v1 *(assumed)*; OR/grouping later.
- Log retention 90 days *(assumed)*.
- Test-run preview *(assumed)*.

## Related

- [[./unknowns]] · [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
