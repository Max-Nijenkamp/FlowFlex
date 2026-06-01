---
type: module
domain: IT & Security
panel: it
module-key: it.reporting
status: planned
color: "#4ADE80"
---

# IT Reporting

IT asset valuation, licence spend, helpdesk performance, and compliance dashboards.

## Core Features

- Asset inventory value and count by type/status
- Licence spend: monthly/annual, utilisation rate, waste (unused seats)
- Helpdesk metrics: ticket volume, resolution time, by category
- Device compliance rate (from MDM)
- Upcoming renewals and warranty expiries
- Access review summary (who has access to what)
- Export reports

## Data Model

No additional tables. Aggregates from `it_assets`, `it_licences`, `it_tickets`, `it_mdm_devices`, `it_access_grants`.

## Filament

**Nav group:** Reporting

- `ItDashboardPage` (custom dashboard) — chart widgets

## Related

- [[domains/it/asset-inventory]]
- [[domains/it/software-licences]]
- [[architecture/performance]]
