---
type: adr
date: 2026-07-03
status: decided
domain: All
color: "#F97316"
---

# Two-panel matcher — ui-strategy row #21 + page blueprint

## Context

Wave 2 v3 propagation found four specced pages sharing one interaction shape the decision table had no row for: a left worklist of unmatched/flagged items vs a right panel of candidate matches, with confirm/override actions — finance bank reconciliation, the AP payment run, procurement's 3-way-match board, and the PO sourcing board. All four cited closest-row #9 (report builder) with a `#9*` flag per the no-invented-rows rule; tracked as [[../_archive/build-history/gap-two-panel-matcher-ui-row-missing|gap]].

## Options Considered

1. Keep `#9*` citations — rejected: report builder documents none of the matcher constraints (paired selection, confidence ranking, audited override, money-tier writes); four consumers is past the improvisation threshold.
2. Add row #21 + a page-blueprints kind — chosen.
3. Defer to first build — rejected: bank-rec is phase 3, cheap to decide now with all four consumers known.

## Decision

Row **#21 — Two-panel matcher** added to [[../architecture/ui-strategy]] (Custom Filament Page + two Livewire panels, no realtime) and a **Two-Panel Matcher** kind added to [[../architecture/patterns/page-blueprints]]. The four consumer specs re-cite #21; the `#9*` flags are removed.

## Consequences

- Bank reconciliation, payment run, match board, and sourcing board build against one canonical blueprint (regions, states, keyboard model, audited override).
- Future matcher-shaped screens (dedupe review, payment allocation) have a home.
- Money-mutating confirms stay on the pessimistic tier per [[decision-2026-07-02-optimistic-locking-standard]].

## Related

- [[../architecture/ui-strategy]] · [[../architecture/patterns/page-blueprints]] · [[decision-2026-07-03-pos-kiosk-ui-row]]
