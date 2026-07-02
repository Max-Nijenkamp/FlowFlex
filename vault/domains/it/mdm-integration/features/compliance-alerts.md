---
domain: it
module: mdm-integration
feature: compliance-alerts
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Compliance Alerts

Flag non-compliant devices and notify IT once per state change, resetting when the device recovers.

## Behaviour

- Triggered inside `SyncMdmDevicesJob` when a device's `compliance_status` **transitions** ([[device-sync]]).
- On non-compliant transition: notify IT and set `compliance_alerted = true`.
- **Once per state change** — a device that stays non-compliant across many hourly syncs alerts once.
- `compliance_alerted` **resets on any state change**, including recovery to compliant, so a later re-transition to non-compliant alerts again.
- Recovery (non-compliant → compliant) clears the flag; a recovery notification is sent *(assumed — mirrors "reset on recovery")*.

## UI

- **Kind**: background — no screen; the notification path within the sync job. Compliance state is visible/filterable in `MdmDeviceResource`.
- **Page**: none. Trigger: `compliance_status` transition during `SyncMdmDevicesJob`.

## Data

- Owns / writes: `it_mdm_devices` (`compliance_status`, `compliance_alerted`) only.
- Reads: nothing cross-domain.
- Cross-domain writes: none — notifies IT via the notification channel, writes no other domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: compliance-status transitions detected by [[device-sync]].
- Feeds: IT notification (in-app/email) — no HR events, no cross-domain event.
- Shared entity: none.

## Unknowns

> [!warning] UNVERIFIED
> Whether recovery (non-compliant → compliant) sends its own notification, or only silently resets `compliance_alerted`, is `*(assumed)*`. Confirm the notification-on-recovery behaviour before build.

## Related

- [[../_module|MDM Integration]] · [[device-sync]] · [[../security|mdm.security]]
