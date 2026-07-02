---
domain: it
module: it-reporting
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# IT Reporting — Unknowns & Assumptions

The source spec carried no explicit `*(assumed)*` markers or `## Open Questions` section. The items below are unverified defaults derived from the spec; they function as authoritative defaults at build time but are overridable via ADR.

---

## Open Questions

1. **Compliance = "compliant devices / total enrolled" — which devices count?**
   The compliance rate is assumed to be enrolled compliant / total enrolled from `it_mdm_devices`. Whether unenrolled or retired devices are excluded from the denominator is unspecified — clarify before writing the compliance aggregate.

2. **Licence waste definition.**
   "Waste" is assumed to be unused-seat cost (assigned seats − active seats × per-seat cost). The exact utilisation signal (last-login? assignment?) that marks a seat "unused" is not specified in the source.

3. **Current period vs historical cache boundary.**
   The 15 min / 1 h TTL split hinges on what counts as the "current period". Assumed to mean any range whose `to` is today/now. Confirm the boundary before implementing the cache-TTL selector.

---

## Unverified

> [!warning] UNVERIFIED — permission naming
> `## Permissions` lists `it.reporting.view`, but the access contract checks `it.reporting.view-any`. Reconcile which permission is authoritative before build (same discrepancy pattern as sibling analytics modules).

> [!warning] UNVERIFIED — source tables must exist and match names
> `it_assets`, `it_licences`, `it_tickets`, `it_mdm_devices`, `it_access_grants` must exist with these exact names. They belong to sibling modules (it.assets is hard; the rest soft) that are themselves `planned` and were stripped to the app/admin shell.

> [!warning] UNVERIFIED — export mechanism + throttle
> The report export action (PDF/CSV) and its named per-company-user throttle are not yet specified (medium security finding open, per [[../../../architecture/security]]).

> [!warning] UNVERIFIED — N+1-free aggregation not yet proven
> The "one grouped query per source table, no N+1" and "soft-dep sections null when inactive" behaviors are intended per the Test Checklist; none are built or passing.

Blueprint per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing in this module is built.
