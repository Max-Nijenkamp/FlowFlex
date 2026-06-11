---
type: module
domain: IT & Security
domain-key: it
panel: it
module-key: it.mdm
status: planned
priority: p3
depends-on: [it.assets, core.billing, core.rbac, foundation.queues]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [encryption, queues]
tables: [it_mdm_config, it_mdm_devices]
permission-prefix: it.mdm
encrypted-fields: ["it_mdm_config.api_key"]
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# MDM Integration

Mobile Device Management integration: sync managed devices from an MDM provider (Jamf, Intune, Kandji), view compliance status, and link devices to assets/employees. (v1 = one provider first — **build-time ADR for provider order**; driver abstraction from day one *(assumed)*.)

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/it/asset-inventory\|it.assets]] | device ↔ asset/employee links |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, sync jobs |

---

## Core Features

- MDM provider connection (Jamf / Intune / Kandji) via API — driver abstraction
- Device sync: pull managed devices, OS version, compliance status, last check-in (incremental cursor)
- Compliance status: compliant / non-compliant / unknown
- Link MDM device to an IT asset record and employee (auto-match by serial number *(assumed)*, manual override)
- Compliance alerts: flag non-compliant devices (notify IT, once per state change)
- Device action triggers (lock, wipe) — proxied to provider; **wipe requires `it.mdm.wipe` permission + confirm + audit**
- Sync schedule (periodic pull)

---

## Data Model

### it_mdm_config — id, company_id (indexed) unique, provider (in set), 🔐 api_key (encrypted), instance_url *(assumed)*, last_synced_at nullable
### it_mdm_devices

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| external_device_id | string | unique `(company_id, external_device_id)` — sync dedupe |
| device_name / os_version | string | |
| serial_number | string nullable | auto-match key |
| compliance_status | string | compliant / non-compliant / unknown |
| compliance_alerted | boolean default false | reset on state change |
| last_checkin_at | timestamp nullable | |
| asset_id / employee_id | ulid nullable | links |

---

## DTOs

### ConnectMdmData — provider (in set), api_key, instance_url — verified against provider before save
### DeviceActionData — device_id, action (in:lock,wipe) — wipe permission-gated

## Services & Actions

- `MdmDriverInterface` — `fetchDevices(cursor)`, `lock(deviceId)`, `wipe(deviceId)`; per-provider drivers
- `SyncMdmDevicesJob` — upsert by external_device_id; serial auto-match to assets; compliance change → alert
- `TriggerDeviceAction::run(DeviceActionData)` — proxied, audited

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `SyncMdmDevicesJob` | default | hourly | upsert dedupe + sync cursor |

---

## Filament

**Nav group:** Devices

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `MdmDeviceResource` | #1 (read-only synced) | compliance filter, link actions, lock/wipe actions |
| `MdmConfigPage` | #7 custom page (form) | connect provider, credentials write-only |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('it.mdm.view-any') && BillingService::hasModule('it.mdm')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`it.mdm.view-any` · `it.mdm.manage-config` · `it.mdm.link` · `it.mdm.lock` · `it.mdm.wipe`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] API key ciphertext; never re-displayed
- [ ] Sync upserts (re-run = no duplicates); serial auto-match links assets
- [ ] Compliance change alerts once; resets on recovery
- [ ] Wipe requires permission + audited; provider API mocked
- [ ] Invalid credentials rejected at connect

---

## Build Manifest

```
database/migrations/xxxx_create_it_mdm_config_table.php
database/migrations/xxxx_create_it_mdm_devices_table.php
app/Models/IT/{MdmConfig,MdmDevice}.php
app/Data/IT/{ConnectMdmData,DeviceActionData}.php
app/Contracts/IT/MdmDriverInterface.php
app/Support/IT/Drivers/{JamfDriver}.php (first provider per ADR)
app/Jobs/IT/SyncMdmDevicesJob.php
app/Actions/IT/TriggerDeviceAction.php
app/Filament/IT/Resources/MdmDeviceResource.php
app/Filament/IT/Pages/MdmConfigPage.php
database/factories/IT/MdmDeviceFactory.php
tests/Feature/IT/{MdmSyncTest,MdmActionTest}.php
```

---

## Related

- [[domains/it/asset-inventory]]
- [[architecture/patterns/encryption]]
- [[architecture/queue-jobs]]
