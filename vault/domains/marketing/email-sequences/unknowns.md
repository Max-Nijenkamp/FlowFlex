---
domain: marketing
module: email-sequences
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Email Sequences — Unknowns

Parent: [[_module]]

## Assumed Items

- Enrolment status is a simple enum, no spatie states *(assumed)*.
- Segment-entry trigger is a nightly diff *(assumed)*; manual/date-based trigger deferred.
- Exit-on-becomes-customer *(assumed)* — the lifecycle signal source is not wired.
- Linear v1 — no open/click branching *(assumed)*.

## Open Questions

> [!warning] UNVERIFIED
> Consent to enter a nurture flow relies entirely on upstream cleanliness (form consent / segment). No explicit per-enrolment marketing-consent check exists. GDPR/PECR would prefer a demonstrable consent record — see the consent-ledger opportunity in [[../_opportunities]].

- Segment-entry diff cadence (nightly vs. event-driven) and how a "just left the segment" exit is detected.
- Which lifecycle event marks a contact as customer for auto-exit?
- Per-step engagement stats: reuse campaign tracking exactly, or a lighter counter?

## Related

- [[_module]] · [[decisions]] · [[../_opportunities]]
