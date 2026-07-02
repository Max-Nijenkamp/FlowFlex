---
domain: it
module: mdm-integration
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# MDM Integration — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR. Design-affecting items should be resolved before implementation begins.

---

## Open Questions

1. **`instance_url` on `it_mdm_config`**

   > [!warning] UNVERIFIED
   > The spec carries `instance_url *(assumed)*`. Jamf and Intune are tenant-scoped (each customer has its own instance/tenant base URL); Kandji may not need one. Confirm whether `instance_url` is required per provider, and its validation rules, before writing the `ConnectMdmData` DTO and the config migration.

2. **Serial-number auto-match to `it_assets`**

   > [!warning] UNVERIFIED
   > Linking a synced device to an asset "by serial number *(assumed)*" assumes serials are unique and consistently formatted across the MDM provider and the assets table. Confirm the match is exact-string (vs. normalized) and define behaviour when two assets share a serial or none match, before writing `SyncMdmDevicesJob`.

3. **Provider order beyond Jamf**

   > [!warning] UNVERIFIED
   > "Jamf first" comes from the source manifest, but the order for Intune vs. Kandji is a **build-time ADR** not yet written. Confirm the sequence before scheduling the second driver.

---

## Assumed Items (verbatim from spec, unverified)

- `*(assumed)*` — `it_mdm_config.instance_url` exists and is the provider instance/tenant base URL (see open question #1)
- `*(assumed)*` — device ↔ asset link is auto-matched **by serial number** (see open question #2)
- `*(assumed)*` — the `MdmDriverInterface` driver abstraction is built from day one even with one provider live
- `*(assumed)*` — provider order beyond "Jamf first" is decided at build time via ADR (see open question #3)
