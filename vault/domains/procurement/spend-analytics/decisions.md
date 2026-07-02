---
domain: procurement
module: spend-analytics
type: decisions
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Spend Analytics — Local Decisions

- **Zero owned tables.** Pure read/aggregation surface — the cleanest bounded context. No migrations, no writes.
- **Cache 1h historical / 15min current.** TTL-only invalidation (no event wiring needed) keeps it simple; small staleness on the current window is acceptable for analytics.
- **Soft-dep sections conditional.** Savings/maverick require catalogue; budget-vs-actual requires finance.budgets — hidden when inactive rather than erroring.
- **Single `view` permission v1.** Per-department scoping deferred.
- **Export throttled** per the security audit.

## Related

- [[_module]] · [[unknowns]] · [[../../../architecture/caching]]
