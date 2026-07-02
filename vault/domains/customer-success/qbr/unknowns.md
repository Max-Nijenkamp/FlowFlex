---
domain: customer-success
module: qbr
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# QBR — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers from spec)

- **Seeded agenda template** — a standard default agenda is assumed; the exact content is not specified.
- **Quarterly cadence + auto-next** — completing a QBR auto-creates the next one a quarter out. The cadence being configurable per account (vs fixed quarterly) is assumed.
- **CSM = account `owner_id`** — shared CS assumption ([[../health-scores/unknowns]]).
- **Deck prep trigger** — whether prep runs on a button, on a pre-QBR schedule, or both, is assumed (both supported).
- **Reminders as notifications** — action-item reminders via `core.notifications`, not cross-domain events.

## Open Questions

- Should the deck be exportable to PDF (spatie/laravel-pdf) for sharing with the customer? Not specified v1; noted as an opportunity ([[../../_opportunities]]).
- Is there a customer-facing QBR view/portal, or is the deck internal-only? v1 = internal-only.
- Should cancelling a QBR still schedule the next one per cadence, or break the chain? Assumed: only completion advances the cadence.

## Implementation Notes

- `outcomes` is required to transition `scheduled → held` — enforced in `RecordOutcomesData` + the state guard.
- Deck sections are conditional on the source module being active (health trend, support summary) — mirror the health renormalisation philosophy of "omit inactive".
