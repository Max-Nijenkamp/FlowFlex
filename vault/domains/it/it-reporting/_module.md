---
domain: it
module: it-reporting
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# IT Reporting

Read-only IT dashboards: asset inventory value, licence spend and waste, helpdesk performance, device compliance, and upcoming renewals/warranties. **Owns no tables** — every metric is aggregated live from other IT modules' data. Nothing here is built yet; this note is the rebuild blueprint following [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

- **module-key:** `it.reporting`
- **panel:** it — nav group **Reporting**
- **priority:** p3
- **permission-prefix:** `it.reporting`
- **tables:** none
- **encrypted-fields:** none (aggregates only — never surfaces sensitive asset/access rows)

---

## Module-key

**Priority:** p3
**Panel:** /it
**Permission prefix:** `it.reporting`
**Tables:** none

## Purpose

A pure read/aggregate layer over the IT domain. It computes asset valuation, licence spend/waste, helpdesk metrics, and compliance rates and renders them as apex-chart widgets on a single dashboard page. **It owns zero tables** — all data is aggregated read-only from the five source tables listed below via their owning modules' read APIs. It never writes, and soft-dependent sections disappear cleanly when their module is inactive.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../asset-inventory/_module\|it.assets]] | asset valuation metrics — blocking |
| Hard | core.billing + core.rbac | module gating + permissions — blocking |
| Soft | [[../software-licences/_module\|it.licences]] | licence spend/waste widget; **hidden** when inactive |
| Soft | [[../helpdesk/_module\|it.helpdesk]] | helpdesk metrics widget; **hidden** when inactive |
| Soft | [[../mdm-integration/_module\|it.mdm]] | device compliance widget; **hidden** when inactive |
| Soft | [[../access-provisioning/_module\|it.access]] | access review summary; **hidden** when inactive |

Soft-dep sections null out in the `ItMetricsData` DTO and their widgets do not render when the module is not active for the company — no errors, no empty scaffolding.

---

## Core Features

- Asset inventory value and count by type/status ([[features/asset-valuation-widget|AssetValueWidget]])
- Licence spend: monthly/annual, utilisation rate, waste — unused seats ([[features/licence-spend-widget|LicenceSpendWidget]], soft-dep)
- Helpdesk metrics: ticket volume, resolution time, by category ([[features/helpdesk-metrics-widget|HelpdeskWidget]], soft-dep)
- Device compliance rate from MDM ([[features/compliance-widget|ComplianceWidget]], soft-dep)
- Upcoming renewals and warranty expiries
- Access review summary — who has access to what (soft-dep)
- Export reports (throttled) — [[features/it-dashboard|ItDashboardPage]]

---

## Build Manifest

```
app/Data/IT/ItMetricsData.php
app/Services/IT/ItAnalyticsService.php
app/Filament/IT/Pages/ItDashboardPage.php
app/Filament/IT/Widgets/{AssetValueWidget,LicenceSpendWidget,HelpdeskWidget,ComplianceWidget}.php
tests/Feature/IT/ItReportingTest.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's it reporting data
- [ ] Module gating: artifacts hidden when `it.reporting` inactive
- [ ] Asset valuation + licence waste math over fixtures
- [ ] Helpdesk resolution averages over fixtures
- [ ] Inactive soft-dep sections hidden, no errors

---

## Cross-Domain Edges

| Direction | Event / integration | Counterpart | Notes |
|---|---|---|---|
| Reads | `it_assets` | it.assets | asset value/count aggregates via its read API (no direct writes) |
| Reads | `it_licences` | it.licences | spend / utilisation / waste aggregates (soft-dep, read-only) |
| Reads | `it_tickets` | it.helpdesk | volume / resolution-time / by-category series (soft-dep, read-only) |
| Reads | `it_mdm_devices` | it.mdm | compliance-rate aggregate (soft-dep, read-only) |
| Reads | `it_access_grants` | it.access | access-review summary (soft-dep, read-only) |
| Fires | none | — | read-only dashboards; emits nothing outbound |
| Consumes | none | — | recomputes live per request (TTL-cached); no listeners |

**Data ownership:** `it.reporting` **owns zero tables**. It only *reads* the five IT source tables (`it_assets`, `it_licences`, `it_tickets`, `it_mdm_devices`, `it_access_grants`) through their owning modules' read APIs and **never writes any table — its own or another domain's** ([[../../../security/data-ownership]]).

---

## Related

- [[architecture|it-reporting.architecture]]
- [[data-model|it-reporting.data-model]]
- [[security|it-reporting.security]]
- [[decisions|it-reporting.decisions]]
- [[unknowns|it-reporting.unknowns]]
- [[features/it-dashboard|ItDashboardPage feature]]
- [[features/asset-valuation-widget|AssetValueWidget feature]]
- [[features/licence-spend-widget|LicenceSpendWidget feature]]
- [[features/helpdesk-metrics-widget|HelpdeskWidget feature]]
- [[features/compliance-widget|ComplianceWidget feature]]
- [[../asset-inventory/_module|it.assets]]
- [[../software-licences/_module|it.licences]]
- [[../../../architecture/patterns/custom-pages]]
- [[../../../architecture/caching]]
- [[../../../architecture/ui-strategy]]
