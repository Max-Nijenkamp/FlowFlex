---
domain: it
module: it-reporting
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# IT Reporting — Data Model

**Tables owned: none.** This module is a pure read/aggregate layer. It creates no migrations and writes nothing. Every metric is computed live (behind a TTL cache) from five source tables owned by sibling IT modules and read via their owning modules' read APIs — never by direct write ([[../../../security/data-ownership]]).

There is **no ERD of owned tables** because none exist. Instead the diagram below is a **read-dependency flowchart**: it-reporting reads (read-only) from each source table.

---

## Source Tables (read-only)

| Table | Owned by | Read for |
|---|---|---|
| `it_assets` | it.assets (hard) | inventory value + count by type/status |
| `it_licences` | it.licences (soft) | monthly/annual spend, utilisation, waste |
| `it_tickets` | it.helpdesk (soft) | ticket volume, resolution time, by category |
| `it_mdm_devices` | it.mdm (soft) | device compliance rate |
| `it_access_grants` | it.access (soft) | access-review summary |

---

## Read Dependency

```mermaid
flowchart LR
  Rep[it.reporting\n(owns NO tables)]
  Rep -->|read-only| A[(it_assets)]
  Rep -.read-only.-> L[(it_licences)]
  Rep -.read-only.-> T[(it_tickets)]
  Rep -.read-only.-> M[(it_mdm_devices)]
  Rep -.read-only.-> G[(it_access_grants)]

  A -->|owned by| Ao[it.assets]
  L -->|owned by| Lo[it.licences]
  T -->|owned by| To[it.helpdesk]
  M -->|owned by| Mo[it.mdm]
  G -->|owned by| Go[it.access]
```

Solid arrow = hard dependency (`it_assets`). Dashed arrows = soft dependencies whose sections null out when the owning module is inactive. All arrows are **read-only** — no arrow points back out of it.reporting into any table.

---

## DTOs

### ItMetricsData (output only)

The single output DTO returned by `ItAnalyticsService::metrics(from, to)`. Soft-dep sections are nullable and become `null` when their module is inactive.

- `period` — `{from, to}` window the metrics cover
- `asset_value_total` — total inventory value (brick/money, integer minor units)
- `asset_breakdown[]` — count + value grouped by type and by status (from `it_assets`)
- `licence_spend?` — `{monthly, annual}` spend + `utilisation_rate` + `waste` (unused seat cost); **null** when it.licences inactive (from `it_licences`)
- `helpdesk_series?` — ticket volume over time, average resolution time, breakdown by category; **null** when it.helpdesk inactive (from `it_tickets`)
- `compliance_rate?` — device compliance percentage; **null** when it.mdm inactive (from `it_mdm_devices`)
- `access_summary?` — who-has-access-to-what rollup; **null** when it.access inactive (from `it_access_grants`)
- `upcoming_items[]` — upcoming licence renewals + warranty expiries within the window

No input DTO — the only inputs are the `from`/`to` `CarbonImmutable` bounds passed directly to the service.
