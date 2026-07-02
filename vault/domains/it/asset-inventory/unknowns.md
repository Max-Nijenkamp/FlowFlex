---
domain: it
module: asset-inventory
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Asset Inventory — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR. Design-affecting items should be resolved before implementation begins.

---

## Open Questions

1. **Finance disposal hint — note only, or an actual finance trigger?**
   The spec marks the retire → finance disposal hint as *(assumed: note only)*. Confirm whether retiring a linked asset should merely surface a note, or fire an event that finance.assets consumes to start a disposal. Affects the `RetireAssetAction` and any cross-domain event.

2. **Warranty alert cadence & window**
   Assumed to fire once at 30 days before expiry. Confirm the 30-day window and the once-only policy (vs. reminders at 30/7/1 days).

3. **Where is condition captured?**
   Assumed on the assignment row on return. Confirm whether condition should also be tracked at other lifecycle points (e.g. on repair intake).

---

## Assumed Items (verbatim from spec, unverified)

> [!warning] UNVERIFIED — retire finance behaviour
> `*(assumed: note only)*` — on retire of a finance-linked asset, a finance disposal hint is surfaced as a **note only**; this module does not initiate the disposal. Scope unverified — see open question #1.

- `*(assumed)*` — warranty expiry alerts fire once at 30 days before expiry, guarded by `warranty_alerted`
- `*(assumed)*` — asset condition is tracked via `condition_note` on the assignment row, captured on return
- `*(assumed)*` — `serial_number` is unique per company only where set (nullable, non-unique when null)
