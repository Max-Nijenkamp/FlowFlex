---
domain: it
module: asset-inventory
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Asset Inventory — Architecture

See also [[_module|asset-inventory._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/patterns/states]], [[../../../architecture/event-bus]], [[../../../architecture/ui-strategy]].

---

## Services & Actions

- `AssignAssetAction::run(AssignAssetData $data): void` — asset must be `in_stock` + employee active; writes an `it_asset_assignments` row + transitions `in_stock → assigned`
- `ReturnAssetAction::run(ReturnAssetData $data): void` — sets `returned_at` + `condition_note` on the open assignment; transitions `assigned → in_stock`
- `RetireAssetAction::run(...): void` — transitions to `retired`; blocked while `assigned`; finance disposal hint when linked *(assumed: note only)*
- `FlagAssetsForReturnListener` on `EmployeeOffboarded` — flags that employee's assigned assets (`return_flagged_at`) + notifies IT; queued, `WithCompanyContext`, per [[../../../architecture/event-bus]]

---

## State Machine

Modelled with spatie/laravel-model-states — see [[../../../architecture/patterns/states]].

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `in_stock` | `assigned` | `it.assets.assign` | assignment row |
| `assigned` | `in_stock` (return) / `in_repair` | return action / repair | `returned_at` + condition note |
| `in_repair` | `assigned` / `in_stock` / `retired` | | |
| `in_stock` / `in_repair` | `retired` | `it.assets.retire` | finance disposal hint when linked *(assumed: note only)* |

State classes: `AssetState`, `InStock`, `Assigned`, `InRepair`, `Retired` (`app/States/IT/Asset/`).

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `WarrantyAlertCommand` | notifications | daily | `warranty_alerted` once-guard |

Warranties expiring within 30 days trigger a single notification; the `warranty_alerted` boolean prevents re-alerting.

---

## Filament Artifacts

**Nav group:** Assets

| Artifact | Kind (ui-strategy row) | Notes |
|---|---|---|
| `AssetResource` | #1 CRUD resource | filters type/status/assignee; assign/return/retire row actions; assignment-history relation manager |
| `AssetExpiryWidget` | #6 widget | warranties expiring within 30d |

Pattern reference: [[../../../architecture/filament-patterns]], [[../../../architecture/ui-strategy]].

**Access contract:** every artifact gates on `canAccess() = Auth::user()->can('it.assets.view-any') && BillingService::hasModule('it.assets')` per [[../../../architecture/filament-patterns]] #1 — see [[security|asset-inventory.security]].
