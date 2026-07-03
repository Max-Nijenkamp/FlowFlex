---
domain: it
module: mdm-integration
feature: device-actions
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Device Actions (Lock / Wipe)

Remote **lock** and **wipe** actions on a synced device, proxied to the provider. Wipe is the destructive, permission-gated path.

## Behaviour

- Row actions on `MdmDeviceResource` build a `DeviceActionData` (`device_id`, `action` in lock/wipe).
- `TriggerDeviceAction::run()` validates the device belongs to the company, then proxies the call to the provider through the resolved driver (`lock` / `wipe`), and **audits** it.
- **`lock`** gates on `it.mdm.lock`.
- **`wipe`** is destructive: requires the dedicated **`it.mdm.wipe`** permission, an explicit **confirm** step, and an **audit** log entry — see [[../security|mdm.security]].
- No local device row is deleted — the action is a provider command; the device's next sync reflects the outcome.

## UI

- **Kind**: simple-resource — row actions on the read-only synced `MdmDeviceResource` (no create/edit form).
- **Page**: `MdmDeviceResource` list at `/app/it/mdm/devices` — table with compliance filter; **Lock** and **Wipe** row actions.
- **Layout**: device table (name, OS, compliance, last check-in, linked asset); Lock/Wipe in the row action menu; Wipe styled destructive with a confirmation modal.
- **Key interactions**: click Lock → confirm → proxy + audit; click Wipe → **permission check + confirm modal** → proxy + audit; toast on dispatch.
- **States**: empty (no devices synced yet → prompt to connect a provider) · loading (dispatching to provider) · error (provider rejects → toast + retry) · selected (row action menu open; Wipe confirm modal focused).
- **Gating**: `it.mdm.view-any` to see rows; `it.mdm.lock` for Lock; **`it.mdm.wipe` for Wipe** (destructive gate).

## Data

- Owns / writes: `it_mdm_devices` (the audited action target) only.
- Reads: `it_assets` for the linked-asset display column (read-only).
- Cross-domain writes: none — the FK link to `it_assets` is read-only; the action itself is a provider command, not a DB write into another domain ([[../../../../security/data-ownership]]).

## Relations

- Consumes: rows populated by [[device-sync]].
- Feeds: provider command (lock/wipe) + activity-log audit entry — no cross-domain event, no HR events.
- Shared entity: `it_assets` owned by it.assets — read for display only.

## Test Checklist

### Unit
- [ ] `DeviceActionData` validation: action limited to lock|wipe; device must belong to the company

### Feature (Pest)
- [ ] `TriggerDeviceAction` proxies to the resolved driver and writes an audit entry; raced double-click fires the provider command once (lockForUpdate)
- [ ] Wipe requires `it.mdm.wipe`; lock requires `it.mdm.lock`; neither deletes the local row
- [ ] Tenant isolation: acting on another company's device id is rejected

### Livewire
- [ ] Lock/Wipe row actions render per permission; Wipe shows destructive confirm modal; provider rejection surfaces toast
- [ ] Resource hidden without `it.mdm.view-any` or with `it.mdm` inactive

## Unknowns

- None specific beyond the module-level assumptions — see [[../unknowns|mdm.unknowns]].

## Related

- [[../_module|MDM Integration]] · [[device-sync]] · [[../security|mdm.security]] · [[../architecture|mdm.architecture]]
