---
domain: it
module: mdm-integration
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# MDM Integration — Decisions

---

## One Provider First (Build-Time ADR for Order)

v1 ships **one** live MDM provider. Which one — and the order the rest follow — is a **build-time ADR**, not fixed in the spec. Per the source manifest, **Jamf is first** (`app/Support/IT/Drivers/JamfDriver.php` is the only driver in the Build Manifest). Intune and Kandji follow as later drivers once the abstraction is proven against a real provider.

---

## Driver Abstraction from Day One

Even with one provider live, `MdmDriverInterface` (`fetchDevices` / `lock` / `wipe`) is built up front *(assumed)*. Adding a provider is then a new driver class resolved from `it_mdm_config.provider` — no changes to the sync job, actions, or Filament layer. This avoids a rewrite when the second provider lands.

---

## Serial Auto-Match to Assets

Synced devices link to `it_assets` by **serial number** match within the company *(assumed)*. `SyncMdmDevicesJob` sets `it_mdm_devices.asset_id` after upsert; a manual override (`it.mdm.link`) covers mismatches. The link is an FK reference set from a **read** of `it_assets` — this module never writes into the assets table ([[../../../security/data-ownership]]).

---

## Compliance Alert Once Per State Change

Non-compliant devices notify IT **once per state transition**, not on every sync. `compliance_alerted` guards the notification; it **resets when the status changes** (including recovery to compliant), so a device that goes non-compliant → compliant → non-compliant alerts twice, but a device that stays non-compliant across ten hourly syncs alerts once.

---

## Wipe Permission-Gated + Audited

Remote wipe is destructive, so it sits behind a dedicated **`it.mdm.wipe`** permission (separate from `lock`), an explicit **confirm** step, and an **audit** log entry. `lock` uses the same proxy+audit path under the softer `it.mdm.lock`. See [[security|mdm.security]].
