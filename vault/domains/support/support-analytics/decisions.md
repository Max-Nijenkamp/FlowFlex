---
domain: support
module: support-analytics
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Support Analytics — Local Decisions

## Decided

- **Analytics owns CSAT, Tickets fires the event.** `TicketResolved` (fired by Tickets) is consumed here; `sup_csat_responses` + the survey mail live here — keeps Tickets thin and CSAT logic in one place. This module is the **v1 consumer** until marketing's P3 CSAT.
- **Only CSAT is a table; everything else aggregates.** All other metrics are read-only aggregate queries over Tickets/SLA tables — no denormalised analytics tables in v1 (cache instead).
- **Cache, not realtime.** Metrics are cached (1h historical / 15min current) and widgets poll 60s — support dashboards don't need Reverb.

## Assumed (overridable via ADR)

- Marketing P3 CSAT supersedes the mail design *(assumed)*.
- CSAT `comment` not sensitive → unencrypted *(assumed)*.

## Related

- [[./unknowns]] · [[../../../architecture/event-bus]] · [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
