---
domain: it
module: asset-inventory
feature: assignment-return
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Assignment & Return

Assign an asset to an employee, return it with a condition note, and retire it — the lifecycle actions on the asset record, with full assignment history.

## Behaviour

- **Assign** (`AssignAssetAction`): asset must be `in_stock` + employee active → writes an `it_asset_assignments` row (`assigned_at`) + transitions `in_stock → assigned`; mirrors `assigned_to_employee_id` on the asset.
- **Return** (`ReturnAssetAction`): asset must be `assigned` → sets `returned_at` + `condition_note` on the open assignment → transitions `assigned → in_stock`.
- **Retire** (`RetireAssetAction`): transitions `in_stock`/`in_repair` → `retired`; **blocked while `assigned`** (return first); finance disposal hint when linked *(assumed: note only)*.
- Assignment history = all `it_asset_assignments` rows for the asset; the open row (`returned_at` null) is the current holder.

## UI

- **Kind**: simple-resource — row actions + relation manager on `AssetResource` (no separate page).
- **Page**: `AssetResource` at `/it/assets` — Assign / Return / Retire actions per row + "Assignment history" relation manager.
- **Layout**: Assign action → modal (employee select, active only); Return action → modal (condition note); Retire → confirm modal (shows finance disposal hint when linked); history table shows employee · assigned_at · returned_at · condition note.
- **Key interactions**: Assign disabled unless `in_stock`; Return disabled unless `assigned`; Retire disabled while `assigned`.
- **States**: empty (no history → "never assigned") · loading (action spinner) · error (assign non-in_stock or retire-while-assigned → rejected with toast) · selected (open assignment highlighted in history).
- **Gating**: assign/return `it.assets.assign`; retire `it.assets.retire`.

## Data

- Owns / writes: `it_assets` (status + `assigned_to_employee_id`) and `it_asset_assignments`.
- Reads: active-employee lookup from hr.profiles for the assignee select.
- Cross-domain writes: via events only — the finance disposal hint is a note, no finance-table write ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing (offboarding auto-flag handled in [[offboarding-return-flags]]).
- Feeds: nothing fired externally — lifecycle is internal to this module.
- Shared entity: `hr_employees` (assignee, read-only); `finance.assets` (soft `fin_asset_id` link).

## Test Checklist

### Unit
- [ ] Assign guard requires `in_stock`; retire guard rejects while `assigned`

### Feature (Pest)
- [ ] Assign writes an `it_asset_assignments` row + transitions `in_stock → assigned` + mirrors `assigned_to_employee_id`
- [ ] Return stamps `returned_at` + `condition_note` and transitions `assigned → in_stock`
- [ ] Concurrent double-assign of the same asset rejected (row lock); retire-while-assigned rejected
- [ ] Actions tenant-scoped: cannot assign company B's asset or to company B's employee

### Livewire
- [ ] Assign / Return actions gated by `it.assets.assign`, Retire by `it.assets.retire`; actions disabled outside the valid state

## Unknowns

- `*(assumed: note only)*` — retire finance disposal hint is a note, not a finance trigger. See [[../unknowns|asset-inventory.unknowns]] (UNVERIFIED).
- `*(assumed)*` — condition captured on return only.

## Related

- [[../_module|Asset Inventory]] · [[asset-record]] · [[offboarding-return-flags]] · [[../architecture|asset-inventory.architecture]] · [[../decisions|asset-inventory.decisions]]
