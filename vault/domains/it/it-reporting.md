---
type: module
domain: IT & Security
domain-key: it
panel: it
module-key: it.reporting
status: planned
priority: p3
depends-on: [it.assets, core.billing, core.rbac]
soft-depends: [it.licences, it.helpdesk, it.mdm, it.access]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: []
permission-prefix: it.reporting
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# IT Reporting

IT asset valuation, licence spend, helpdesk performance, and compliance dashboards. Owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/it/asset-inventory\|it.assets]] | asset metrics |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | licences / helpdesk / mdm / access | their sections hidden when inactive |

---

## Core Features

- Asset inventory value and count by type/status
- Licence spend: monthly/annual, utilisation rate, waste (unused seats)
- Helpdesk metrics: ticket volume, resolution time, by category
- Device compliance rate (from MDM)
- Upcoming renewals and warranty expiries
- Access review summary (who has access to what)
- Export reports

---

## Data Model

No additional tables. Aggregates from `it_assets`, `it_licences`, `it_tickets`, `it_mdm_devices`, `it_access_grants`.

## DTOs

Output only: `ItMetricsData` — asset breakdowns, spend, helpdesk series, compliance rate, upcoming items.

## Services & Actions

- `ItAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): ItMetricsData` — soft-dep sections null when modules inactive; brick/money; no N+1

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:it:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

---

## Filament

**Nav group:** Reporting

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ItDashboardPage` | #6 dashboard page + apex charts | soft-dep sections conditional; export |

---

## Permissions

`it.reporting.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Asset valuation + licence waste math over fixtures
- [ ] Helpdesk resolution averages over fixtures
- [ ] Inactive soft-dep sections hidden, no errors

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

## Related

- [[domains/it/asset-inventory]]
- [[domains/it/software-licences]]
- [[architecture/caching]]
