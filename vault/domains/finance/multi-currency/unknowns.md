---
domain: finance
module: multi-currency
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Multi-Currency — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Manual rates v1** — exchange rates are entered manually in v1; an API feed (daily rates) is a deferred hook. *(assumed)*
- **Reversing revaluation** — `RevalueOpenBalancesCommand` writes unrealised FX entries that reverse the next period. *(assumed)*

UNVERIFIED:
- The spec does not enumerate which GL accounts the realised/unrealised FX gain/loss postings target.
- The per-record `currency` + `exchange_rate` columns are owned by consuming modules; their exact migration and shape are intended to be added by those modules and are not specified here.
- Currency display formatting "per company locale" is described but the locale source/format rules are not detailed.
