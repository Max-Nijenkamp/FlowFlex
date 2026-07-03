---
domain: it
module: asset-inventory
feature: warranty-alerts
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Warranty Alerts

Surface assets whose warranty is about to expire — a dashboard widget plus a daily notification job.

## Behaviour

- **Widget** (`AssetExpiryWidget`): lists `it_assets` with `warranty_expiry` within 30 days.
- **Job** (`WarrantyAlertCommand`, daily, notifications queue): finds assets expiring within 30 days that have not yet been alerted → notifies IT → sets `warranty_alerted = true`.
- Fires **once** per asset — the `warranty_alerted` boolean is the once-guard *(assumed)*.

## UI

- **Kind**: widget + background — a Filament widget on the IT dashboard plus a scheduled command (no page of its own).
- **Page**: `AssetExpiryWidget` (dashboard widget, ui-strategy row #6); `WarrantyAlertCommand` runs daily (no UI).
- **Layout**: widget = compact list/table of assets expiring within 30 days (name · tag · warranty date · days remaining), newest-expiry first.
- **Key interactions**: click a widget row → open the asset in `AssetResource`. The command emits a notification into IT's core.notifications inbox.
- **States**: empty (nothing expiring → "no warranties expiring soon") · loading (widget skeleton) · error (job failure → queue retry) · selected (row → asset record).
- **Gating**: widget visible with `it.assets.view-any`; the command is system-scheduled (per-company via `WithCompanyContext`).

## Data

- Owns / writes: `it_assets` (`warranty_alerted` once-guard) only.
- Reads: `it_assets.warranty_expiry` to compute the 30-day window.
- Cross-domain writes: none — notification emitted via core.notifications, no other domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: an IT warranty-expiry notification via core.notifications (no domain event fired).
- Shared entity: none beyond this module's `it_assets`.

## Test Checklist

### Unit
- [ ] 30-day window selects assets with `warranty_expiry` within 30 days and `warranty_alerted = false`

### Feature (Pest)
- [ ] `WarrantyAlertCommand` notifies IT once and sets `warranty_alerted = true`; a second run does not re-alert
- [ ] Command runs per-company under `WithCompanyContext`; no cross-tenant leakage

### Livewire
- [ ] `AssetExpiryWidget` lists assets expiring within 30d and is visible only with `it.assets.view-any`

## Unknowns

- `*(assumed)*` — 30-day window, once-only alert guarded by `warranty_alerted` (vs. reminders at 30/7/1 days). See [[../unknowns|asset-inventory.unknowns]].

## Related

- [[../_module|Asset Inventory]] · [[asset-record]] · [[../architecture|asset-inventory.architecture]] · [[../../../../architecture/queue-jobs]]
