---
domain: it
module: mdm-integration
feature: device-sync
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Device Sync

Hourly background pull of managed devices from the connected MDM provider into `it_mdm_devices`, with serial auto-match to IT assets.

## Behaviour

- `SyncMdmDevicesJob` runs **hourly** on the queue ([[../../../../architecture/queue-jobs]]).
- Resolves the driver from `it_mdm_config.provider` and calls `fetchDevices(cursor)` — the cursor (`last_synced_at`) makes each run **incremental**.
- For each device it captures device name, **OS version**, **compliance status**, and **last check-in**.
- **Upserts by `external_device_id`** (unique `(company_id, external_device_id)`) — re-runs never duplicate rows.
- **Serial auto-match to `it_assets`**: when a device's `serial_number` matches a company asset, `asset_id` is set *(assumed)*; a manual override exists via `it.mdm.link`.
- A `compliance_status` transition hands off to the alert path ([[compliance-alerts]]).

## UI

- **Kind**: background — no screen; a scheduled queue job. Results surface in `MdmDeviceResource`.
- **Page**: none. Trigger: hourly scheduler → `SyncMdmDevicesJob`.

## Data

- Owns / writes: `it_mdm_devices` (upsert of synced rows; sets `asset_id` from the match) and updates `it_mdm_config.last_synced_at` (the cursor).
- Reads: `it_assets` (by `serial_number`) to resolve the auto-match link.
- Cross-domain writes: none — `asset_id` is an FK reference set from a **read** of `it_assets`, never a write into the assets table ([[../../../../security/data-ownership]]).

## Relations

- Consumes: the stored connection from [[provider-connection]] (provider + credentials).
- Feeds: [[compliance-alerts]] (fires on compliance-status change); populates rows for [[device-actions]].
- Shared entity: `it_assets` owned by it.assets — read-matched by serial, never written.

## Unknowns

> [!warning] UNVERIFIED
> Serial auto-match is `*(assumed)*` — exact-string vs. normalized match, and behaviour on duplicate/no match, is unconfirmed. See [[../unknowns|mdm.unknowns]] #2.

## Related

- [[../_module|MDM Integration]] · [[provider-connection]] · [[compliance-alerts]] · [[device-actions]] · [[../../asset-inventory/_module|it.assets]]
