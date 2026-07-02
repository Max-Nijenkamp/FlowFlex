---
domain: it
module: asset-inventory
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Asset Inventory — Decisions

---

## Retire: Finance Disposal Hint (note only)

When a retired asset is linked to a finance fixed asset (`fin_asset_id` set), retiring it surfaces a finance disposal hint. The planned behaviour is a **note only** — this module does not write to finance tables; any disposal is initiated in finance.assets. See [[unknowns|asset-inventory.unknowns]] for the unverified scope of this hint.

---

## Warranty Alerts: Once at 30 Days

Warranty expiry alerts fire **once** when an asset's `warranty_expiry` falls within 30 days. The `warranty_alerted` boolean acts as a once-guard so `WarrantyAlertCommand` (daily) does not re-notify. Chosen to avoid alert fatigue; a repeated / escalating cadence was not specified.

---

## Condition Tracking on Return

Asset condition is captured **on return** via a free-text `condition_note` on the `it_asset_assignments` row (not on the asset itself, and not on assignment). This keeps condition tied to a specific assignment period rather than the asset's whole lifetime.

---

## Implementation Notes

- Assignment history lives in `it_asset_assignments`; the open row (`returned_at` null) is the current holder, mirrored by `it_assets.assigned_to_employee_id` for quick filtering
- Offboarding effects are handled by this module's own listener reacting to `EmployeeOffboarded`; hr.profiles never writes `it_assets` directly ([[../../../security/data-ownership]])
- Retire is blocked while an asset is `assigned` — the asset must be returned first
