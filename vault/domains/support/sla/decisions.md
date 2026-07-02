---
domain: support
module: sla
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# SLA Management — Local Decisions

## Decided

- **Events table for idempotency, not booleans on tickets.** `sup_sla_events` with unique `(ticket_id, type)` guarantees each met/warning/breach fires once and gives the compliance report a clean event log — instead of mutable flags on `sup_tickets`.
- **Pause derived, not stored as a running counter.** Elapsed time is computed from ticket status-transition timestamps (`waiting_on_customer` windows) rather than a persisted ticking counter — avoids drift and cron-frequency coupling.
- **Business hours read from core.settings.** SLA does not own timezone/business-hours config; it reads company settings (read-only) so all domains share one source.

## Assumed (overridable via ADR)

- Warning threshold fixed at 80% *(assumed)* — could be per-policy.
- Pause windows derived purely from status timestamps *(assumed)*.

## Related

- [[./unknowns]] · [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
