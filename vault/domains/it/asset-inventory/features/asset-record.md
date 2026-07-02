---
domain: it
module: asset-inventory
feature: asset-record
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Asset Record

CRUD of IT assets — the core inventory entity (laptops, phones, monitors, licences) with type, serial, tag, warranty and status.

## Behaviour

- Create / edit / delete an `it_assets` row: name, type (laptop, desktop, phone, monitor, peripheral), serial number, asset tag, purchase date, warranty expiry, cost.
- `asset_tag` unique per company; `serial_number` unique per company where set.
- Status is driven by the state machine (`in_stock → assigned → in_repair → retired`) — set via lifecycle actions, not edited freely on the form ([[../architecture|asset-inventory.architecture]]).
- Deleting is a soft delete (`deleted_at`).

## UI

- **Kind**: simple-resource — standard Filament Resource (table + form) for `it_assets` ([[../../../../architecture/patterns/filament-resource-checklist]]).
- **Page**: `AssetResource` at `/it/assets` (list + create/edit).
- **Layout**: table columns name · type · asset tag · serial · status badge · assignee · warranty expiry; form groups identity (name/type/tag/serial), purchase (date/cost/warranty), finance link (`fin_asset_id` when finance.assets active).
- **Key interactions**: filters by type / status / assignee; create + edit form; assignment-history relation manager on the record; assign/return/retire row actions (see [[assignment-return]]).
- **States**: empty (no assets → "add your first asset" CTA) · loading (table skeleton) · error (duplicate `asset_tag` → inline validation) · selected (row → edit / infolist).
- **Gating**: view `it.assets.view-any`; create/edit/delete `it.assets.manage`.

## Data

- Owns / writes: `it_assets` only.
- Reads: employee list from hr.profiles for the assignee column/filter (read-only lookup).
- Cross-domain writes: via events only — never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing directly (offboarding flags handled in [[offboarding-return-flags]]).
- Feeds: nothing (no events fired by plain CRUD).
- Shared entity: `hr_employees` owned by hr.profiles — referenced as assignee, never written.

## Unknowns

- `*(assumed)*` — `serial_number` unique per company only where set (nullable). See [[../unknowns|asset-inventory.unknowns]].

## Related

- [[../_module|Asset Inventory]] · [[assignment-return]] · [[warranty-alerts]] · [[../data-model|asset-inventory.data-model]] · [[../../../../architecture/patterns/filament-resource-checklist]]
