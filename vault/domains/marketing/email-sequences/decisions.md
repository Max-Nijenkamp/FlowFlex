---
domain: marketing
module: email-sequences
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Email Sequences — Decisions

Parent: [[_module]]

## ADR: Cursor-based advancement, idempotent within window

- **Decision:** `AdvanceMarketingSequencesCommand` sweeps `next_step_at <= now AND status=active` every 15 min; the cursor moves inside a transaction so a re-run in the same window can't double-send.
- **Consequences:** Advancement is idempotent; wait_days honoured without a per-enrolment scheduler.

## ADR: Linear sequences v1 (no branching)

- **Decision:** Steps are strictly ordered; branch-by-open/click is deferred *(assumed)*.
- **Consequences:** Simpler engine; branching is an additive later change (candidate journey builder — [[../_opportunities]]).

## ADR: Suppression shared with campaigns

- **Decision:** Enrolment + advancement honour `mkt_unsubscribes` owned by [[../campaigns/_module|campaigns]]; an unsubscribe from either channel suppresses both.
- **Consequences:** One marketing opt-out respected everywhere.

## ADR: Distinct from sales-sequences

- **Decision:** This is bulk automation; 1:1 rep cadences live in [[../../crm/sales-sequences/_module|crm.sales-sequences]]. No shared tables.
- **Consequences:** Two separate modules, no ownership overlap ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[architecture]] · [[unknowns]]
