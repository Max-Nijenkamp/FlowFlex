---
domain: finance
module: fixed-assets
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Fixed Assets — Decisions

## Depreciation methods deferred for v1

The spec lists three methods (straight-line, declining balance, units of production) but intends v1 to ship **straight-line + declining only**; units-of-production is deferred *(assumed)*. Overridable via ADR.

## Final-period rounding absorption

Straight-line depreciation is intended to absorb all rounding into the final period so accumulated depreciation lands exactly on `cost − salvage` (integer-cent arithmetic via brick/money). This is a correctness rule, not a preference.

## Declining-balance salvage floor

Declining-balance is intended never to depreciate an asset below its salvage value; the period charge is clamped so NBV ≥ salvage.

## GL coupling, not events

Depreciation and disposal entries are intended to post via direct in-domain `LedgerService::post` calls rather than cross-domain events — both modules live in the finance domain. See [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] context.

See [[unknowns]].
