---
tags: [flowflex, domain/it, mdm, devices, phase/4]
domain: IT & Security
panel: it
color: "#4F46E5"
status: planned
last_updated: 2026-05-08
---

# MDM & Device Management

Enrol, monitor, and manage company devices — laptops, phones, tablets — from one place. Push policies, enforce encryption, remotely wipe lost devices. Replaces Jamf + Microsoft Intune juggling for SMBs that don't need enterprise complexity.

**Who uses it:** IT admins
**Filament Panel:** `it`
**Depends on:** Core, [[IT Asset Management]], [[SSO & Identity Provider]]
**Phase:** 4

---

## Features

### Device Enrolment

- macOS: MDM profile via Apple Business Manager or direct download
- iOS/iPadOS: ABM DEP enrolment or manual profile install
- Android: Android Enterprise (work profile or fully managed)
- Windows: Microsoft MDM enrollment via Intune-compatible protocol
- Automated enrolment: new device ships pre-enrolled via ABM/Android Zero-Touch
- Self-enrolment: employee scans QR code or visits enrol URL

### Device Inventory

- All enrolled devices visible: name, model, OS version, serial number, assigned user
- Health status: compliant / non-compliant / offline / lost
- Last seen, last sync time
- Sync with [[IT Asset Management]]: enrolled devices auto-appear as assets
- Software inventory: installed applications list per device
- Hardware info: CPU, RAM, storage, battery health

### Policy Management

- Configuration profiles: Wi-Fi, VPN, email account, proxy settings pushed automatically
- Security policies:
  - Force disk encryption (FileVault for macOS, BitLocker for Windows, device encryption Android/iOS)
  - Screen lock timeout
  - Minimum passcode length and complexity
  - Block USB storage
  - Block installing unapproved apps
- App deployment: push approved apps to devices silently (no user interaction needed)
- App blacklist: flag if disallowed apps detected

### Remote Actions

- Remote lock: lock screen immediately (lost device)
- Remote wipe: factory reset (stolen device, employee leaves)
- Remote wipe (selective): wipe work profile only (BYOD — personal data preserved)
- Push message: show message on device lock screen ("Please return to IT")
- Restart device
- All remote actions logged with timestamp, action, performed by

### Compliance Monitoring

- Compliance rules: define what "compliant" means (encrypted, patched, no jailbreak)
- Non-compliant alert: notify IT admin + optionally notify user
- Block access: integrate with SSO — non-compliant device cannot authenticate
- Jailbreak/root detection
- OS version enforcement: minimum version required

### BYOD (Bring Your Own Device)

- Work profile on Android: separate container for work apps and data
- iOS Managed Open In: controls which apps can open work documents
- Personal data untouched: remote wipe only removes work profile
- Employee self-enrolment with clear consent screen

---

## Database Tables (3)

### `it_managed_devices`
| Column | Type | Notes |
|---|---|---|
| `asset_id` | ulid FK nullable | → it_assets |
| `assigned_user_id` | ulid FK nullable | |
| `platform` | enum | `macos`, `ios`, `android`, `windows` |
| `model` | string | |
| `os_version` | string | |
| `serial_number` | string nullable | |
| `mdm_udid` | string unique | |
| `enrolment_type` | enum | `dep`, `manual`, `qr` |
| `enrolled_at` | timestamp | |
| `last_synced_at` | timestamp nullable | |
| `compliance_status` | enum | `compliant`, `non_compliant`, `unknown` |
| `is_encrypted` | boolean nullable | |
| `is_jailbroken` | boolean nullable | |
| `status` | enum | `active`, `lost`, `wiped`, `retired` |

### `it_device_policies`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `platform` | enum | `macos`, `ios`, `android`, `windows`, `all` |
| `rules` | json | [{key, expected_value}] |
| `payload` | json | configuration profile data |
| `is_active` | boolean | |

### `it_device_remote_actions`
| Column | Type | Notes |
|---|---|---|
| `device_id` | ulid FK | |
| `action` | enum | `lock`, `wipe`, `wipe_work_profile`, `message`, `restart` |
| `initiated_by` | ulid FK | |
| `message` | string nullable | |
| `status` | enum | `pending`, `delivered`, `failed` |
| `delivered_at` | timestamp nullable | |

---

## Permissions

```
it.mdm.view
it.mdm.enrol
it.mdm.manage-policies
it.mdm.remote-lock
it.mdm.remote-wipe
it.mdm.view-compliance
```

---

## Competitor Comparison

| Feature | FlowFlex | Jamf | Microsoft Intune | Kandji |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€€) | ❌ (€6/user/mo) | ❌ (€€) |
| macOS + iOS + Android + Windows | ✅ | macOS/iOS | ✅ | macOS/iOS |
| BYOD work profile | ✅ | ✅ | ✅ | ✅ |
| Integrated with HR offboarding | ✅ | ❌ | ❌ | ❌ |
| SSO compliance gate | ✅ | partial | ✅ (Conditional Access) | ❌ |
| Asset inventory sync | ✅ | ❌ | ❌ | ❌ |

---

## Related

- [[IT Overview]]
- [[IT Asset Management]]
- [[SSO & Identity Provider]]
- [[Security & Compliance]]
- [[Access & Permissions Audit]]
