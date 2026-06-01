---
type: module
domain: IT & Security
panel: it
module-key: it.mdm
status: planned
color: "#4ADE80"
---

# MDM Integration

Mobile Device Management integration: sync managed devices from an MDM provider (Jamf, Intune, Kandji), view compliance status, and link devices to assets/employees.

## Core Features

- MDM provider connection (Jamf, Microsoft Intune, Kandji) via API
- Device sync: pull managed devices, OS version, compliance status, last check-in
- Compliance status: compliant / non-compliant / unknown
- Link MDM device to an IT asset record and employee
- Compliance alerts: flag non-compliant devices
- Device action triggers (lock, wipe) — proxied to MDM provider
- Sync schedule (periodic pull)

## Data Model

| Table | Key Columns |
|---|---|
| `it_mdm_config` | company_id, provider, api_key (encrypted), last_synced_at |
| `it_mdm_devices` | company_id, external_device_id, device_name, os_version, compliance_status, last_checkin_at, asset_id, employee_id |

## Filament

**Nav group:** Devices

- `MdmDeviceResource` — read-only synced device list, compliance filter
- `MdmConfigPage` (custom page) — connect provider

## Cross-Domain / Security

- API credentials encrypted (see [[architecture/patterns/encryption]])
- Sync via scheduled queue job

## Related

- [[domains/it/asset-inventory]]
- [[architecture/queue-jobs]]
