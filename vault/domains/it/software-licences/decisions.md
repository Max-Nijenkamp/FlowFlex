---
domain: it
module: software-licences
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Software Licences — Decisions

---

## brick/money for Cost & Waste Math

All monetary arithmetic — per-seat cost, monthly/annual spend, and waste (unused seats × cost) — uses brick/money over integer minor-currency amounts (`cost_per_seat_cents`). No raw float math. `LicenceService::utilisation` returns `waste_cents` as an integer computed through brick/money for rounding consistency. See [[../../../architecture/packages]] (brick/money section).

---

## Renewal Alert: Once Per Cycle, Reset on Renewal-Date Change

`LicenceRenewalAlertCommand` runs daily and notifies 30 days before `renewal_date`. The `renewal_alerted_at` timestamp is the once-guard: set when the alert fires, so a licence is alerted at most once per renewal cycle. When `renewal_date` changes (renewal completed or rescheduled), `renewal_alerted_at` is cleared so the next cycle can alert again.

---

## Over-Capacity & Duplicate Seat Rejection

`AssignSeatAction` rejects two conditions at the action boundary:
- **Over-capacity** — no free seats (active assignments ≥ `total_seats`) → "All seats are in use."
- **Duplicate active seat** — the employee already holds an active (non-revoked) seat on this licence → rejected via the unique `(licence_id, employee_id)` active constraint.

Both are enforced in `AssignSeatData` validation and the action, backed by the DB unique index as the last line of defence.

---

## finance.expenses Link is Report-Only

The soft dependency on finance.expenses is spend-visibility only: licence cost totals are surfaced as a read-only report link. This module fires no event to finance and writes nothing in the finance domain. If bidirectional spend integration is later required, it must go through the event bus and be captured in a new ADR *(assumed)*.

---

## Implementation Notes

- `FlagSeatsForReclaimListener` writes only `it_licence_assignments` (`reclaim_flagged_at`); it never mutates HR tables — the offboarding effect is fully owned here ([[../../../security/data-ownership]]).
- Utilisation bar in `LicenceResource` renders used/total from active assignments; waste from `LicenceService::utilisation`.
