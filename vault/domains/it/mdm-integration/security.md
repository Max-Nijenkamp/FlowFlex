---
domain: it
module: mdm-integration
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# MDM Integration — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../architecture/patterns/encryption]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `it.mdm.view-any` | View synced devices + config page |
| `it.mdm.manage-config` | Connect / change the MDM provider connection |
| `it.mdm.link` | Link/unlink a device to an IT asset (override auto-match) |
| `it.mdm.lock` | Trigger a remote lock on a device |
| `it.mdm.wipe` | Trigger a remote **wipe** — the destructive gate |

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('it.mdm.view-any')
           && BillingService::hasModule('it.mdm')
```

Per [[../../../architecture/filament-patterns]] #1 — the custom `MdmConfigPage` must state `canAccess()` explicitly. Per-action verbs (`lock`, `wipe`, `link`, `manage-config`) gate the individual row actions and the config form on top of `view-any`.

---

## Tenant Isolation

- All queries scoped by `company_id` via `BelongsToCompany` + `CompanyScope`
- `it_mdm_config.company_id` is **unique** — one provider connection per company; no cross-tenant credential reuse
- `DeviceActionData` validates `device_id` belongs to the current company before proxying to the provider
- Serial auto-match only considers `it_assets` in the same company

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].

---

## Encryption

The provider API key is the sensitive secret in this module.

- `it_mdm_config.api_key` uses the **`encrypted` cast** on a **`text` column** (ciphertext is longer than the plaintext — never `string`) per [[../../../architecture/patterns/encryption]].
- The key is **write-only in the UI**: submitted once through `ConnectMdmData`, verified against the provider, then stored encrypted. It is **never re-displayed** — the config form shows a masked "connected" state, and edits require re-entering the key.
- Ciphertext-at-rest is asserted in the test checklist ("API key ciphertext; never re-displayed").

---

## Wipe Gate

Remote wipe is destructive and irreversible on the device, so it carries the strictest gate:

- Requires the dedicated **`it.mdm.wipe`** permission (separate from `it.mdm.lock`)
- Requires an explicit **confirmation** step in the UI before dispatch
- The action is **audited** (spatie/laravel-activitylog) — who wiped which device, when
- `TriggerDeviceAction` proxies to the provider through the driver only after the permission + confirm checks pass

`lock` follows the same proxy+audit path but gates on the less-destructive `it.mdm.lock`.
