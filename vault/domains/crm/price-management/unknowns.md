---
domain: crm
module: price-management
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Price Management — Unknowns & Open Questions

## Assumptions

- *(assumed)* Promotions are modelled as price-book entries carrying `valid_from` / `valid_until`, not a separate promotions table.
- *(assumed)* The margin-guard threshold is configurable per company (default value TBD).
- *(assumed)* The single-default invariant is enforced via a partial unique index plus a service guard.

## Open Questions

- How are overlapping promotional windows for the same (book, product) resolved — most recent `valid_from` wins, or lowest price wins?
- Is the margin threshold an absolute amount, a percentage of cost, or both?
- For multi-currency: does a price book hold a single currency (as modelled) or can entries override currency per line?
- Does `AssignPriceBookAction` allow both account-level and segment-level assignment on the same account, and if so which wins? (Resolution order says account, but the assignment UX is undecided.)
