---
domain: it
module: mdm-integration
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# MDM Integration

Mobile Device Management integration: sync managed devices from an MDM provider (Jamf, Intune, Kandji), view compliance status, and link devices to IT assets. Owns `it_mdm_config` + `it_mdm_devices`; links synced devices to `it_assets` by serial number.

> **v1 = one provider first.** Provider order is a **build-time ADR** (Jamf first per the source manifest — see [[decisions|mdm.decisions]]). The **driver abstraction (`MdmDriverInterface`) ships from day one** *(assumed)* so a second provider is a new driver class, not a rewrite. Credentials are **🔐 encrypted** and the **wipe action is permission-gated + audited**.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../asset-inventory/_module\|it.assets]] | device ↔ asset links (serial auto-match) |
| Hard | core.billing + core.rbac | gating + permissions |
| Hard | foundation.queues | hourly `SyncMdmDevicesJob` runs on the queue |

No HR events — devices link to `it_assets` only; there is no employee-lifecycle coupling in this module.

---

## Core Features

- MDM provider connection (Jamf / Intune / Kandji) via API — driver abstraction, one provider live in v1
- Device sync: pull managed devices, OS version, compliance status, last check-in (incremental cursor)
- Compliance status: compliant / non-compliant / unknown
- Link MDM device to an IT asset record (auto-match by serial number *(assumed)*, manual override)
- Compliance alerts: flag non-compliant devices (notify IT, once per state change, reset on recovery)
- Device action triggers (lock, wipe) — proxied to provider; **wipe requires `it.mdm.wipe` permission + confirm + audit**
- Sync schedule (hourly periodic pull)

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] API key ciphertext; never re-displayed
- [ ] Sync upserts (re-run = no duplicates); serial auto-match links assets
- [ ] Compliance change alerts once; resets on recovery
- [ ] Wipe requires permission + audited; provider API mocked
- [ ] Invalid credentials rejected at connect

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `it_assets` (by serial_number) | it.assets | Serial auto-match sets `it_mdm_devices.asset_id`; no write into `it_assets` |
| Reads | asset record for display | it.assets | Device detail surfaces the linked asset; read-only |

No events fired or consumed. **No HR events** — this module has no employee-lifecycle coupling. Cross-domain effect is limited to reading `it_assets` for the serial auto-match link.

**Data ownership:** `it.mdm` writes only `it_mdm_config` + `it_mdm_devices`; the link to `it_assets` is an FK reference set from a read match, never a write into another domain's table ([[../../../security/data-ownership]]).

---

## Related

- [[architecture|mdm.architecture]]
- [[data-model|mdm.data-model]]
- [[security|mdm.security]]
- [[decisions|mdm.decisions]]
- [[unknowns|mdm.unknowns]]
- [[features/provider-connection|provider-connection feature]]
- [[features/device-sync|device-sync feature]]
- [[features/compliance-alerts|compliance-alerts feature]]
- [[features/device-actions|device-actions feature]]
- [[../asset-inventory/_module|it.assets]]
- [[../../../architecture/patterns/encryption]]
- [[../../../architecture/queue-jobs]]
