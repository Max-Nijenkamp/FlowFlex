---
domain: it
module: mdm-integration
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# MDM Integration — Architecture

See also [[_module|mdm._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/patterns/encryption]], [[../../../architecture/queue-jobs]], [[../../../architecture/ui-strategy]].

---

## Services & Actions

- `MdmDriverInterface` — the day-one abstraction. Methods: `fetchDevices(cursor)`, `lock(deviceId)`, `wipe(deviceId)`. One concrete driver per provider (`JamfDriver` first per ADR; Intune/Kandji added as new classes, no rewrite). The active driver is resolved from `it_mdm_config.provider`.
- `SyncMdmDevicesJob` — pulls managed devices via the resolved driver, then **upserts by `external_device_id`** (unique per company → re-run yields no duplicates). After upsert it runs **serial auto-match to `it_assets`** (sets `asset_id` when a company asset shares the serial) and fires a **compliance-change alert** when a device's `compliance_status` transitions (see [[features/compliance-alerts|compliance-alerts]]).
- `TriggerDeviceAction::run(DeviceActionData)` — proxies `lock` / `wipe` to the provider through the driver, then **audits** the action. `wipe` is permission-gated (`it.mdm.wipe`) — see [[security|mdm.security]].

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `SyncMdmDevicesJob` | default | hourly | upsert dedupe by `external_device_id` + sync cursor (`last_synced_at`) |

The cursor makes each run **incremental** — only devices changed since the last successful sync are pulled. Upsert keying on `(company_id, external_device_id)` guarantees re-runs never duplicate rows. See [[../../../architecture/queue-jobs]].

---

## Filament Artifacts

**Nav group:** Devices

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `MdmDeviceResource` | #1 read-only synced (simple-resource) | rows are sync-owned (no create/edit); compliance filter; row actions **lock / wipe** |
| `MdmConfigPage` | #7 custom page (form) | connect provider, credentials write-only |

`MdmDeviceResource` is read-only because rows originate from the provider sync, not user input — only the `lock` / `wipe` row actions mutate anything, and they proxy to the provider. `MdmConfigPage` is a custom page because connecting a provider is a verify-then-save credential flow, not CRUD.

**Access contract:** every artifact gates on `canAccess() = Auth::user()->can('it.mdm.view-any') && BillingService::hasModule('it.mdm')` per [[../../../architecture/filament-patterns]] #1 — the custom page states it explicitly. See [[security|mdm.security]].

Pattern reference: [[../../../architecture/patterns/custom-pages]], [[../../../architecture/ui-strategy]].

---

## Search & Realtime

No Meilisearch index and no Reverb channel planned. Device list is a scoped Filament table; compliance changes surface through the alert path, not live broadcast.
