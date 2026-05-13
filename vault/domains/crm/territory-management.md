---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.territory
status: planned
color: "#4ADE80"
---

# Territory Management

> Sales territory assignment, rep ownership rules, territory-level reporting, and conflict resolution for companies with geographic or vertical sales structures.

**Panel:** `crm`
**Module key:** `crm.territory`

## What It Does

Territory Management allows companies with structured sales teams to define geographic, industry, or account-based territories and assign them to specific reps. When a new contact or deal is created, territory rules auto-assign it to the correct rep based on the contact's country, industry, company size, or custom field. Territory-level reporting shows pipeline, win rate, and quota attainment per territory. Conflicts (two reps claiming the same account) are flagged and resolved by the sales manager.

## Features

### Core
- Territory records: name, description, owner (primary rep), backup rep, and assignment rules
- Assignment rules: trigger on contact/company attribute (country, industry, company size, custom field) — configurable AND/OR logic
- Auto-assignment: when a new contact or deal is created and matches a territory rule, `owner` is set to the territory rep automatically
- Territory list: all territories with owner, contact count, and current pipeline value
- Manual override: sales manager can reassign a contact to a different territory at any time — audit logged

### Advanced
- Territory hierarchy: parent territory (e.g. EMEA) → child territories (UK, Netherlands, Germany) — roll-up reporting at each level
- Quota assignment: annual or quarterly quota assigned per territory — tracked against won deal value
- Conflict resolution: when two territories' rules both match a contact, a conflict is raised — sales manager resolves by assigning to one territory
- Coverage gaps: territories with no contacts assigned or no active deals — surfaced in territory analytics
- Territory reassignment batch: when a rep leaves, reassign all their contacts and open deals to another rep in bulk

### AI-Powered
- Territory balance analysis: AI analyses pipeline distribution across territories and flags imbalances — one rep with 80% of the pipeline while another has none — suggests rebalancing
- Optimal territory design: based on contact density and historical win rates by geography/industry, AI suggests an optimal territory structure

## Data Model

```erDiagram
    crm_territories {
        ulid id PK
        ulid company_id FK
        string name
        ulid parent_territory_id FK
        ulid primary_rep_id FK
        ulid backup_rep_id FK
        json assignment_rules
        decimal quota
        string quota_period
        timestamps created_at/updated_at
    }

    crm_territory_assignments {
        ulid id PK
        ulid territory_id FK
        string assignable_type
        ulid assignable_id FK
        timestamp assigned_at
        ulid assigned_by FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `assignment_rules` | JSON rule tree — same format as Customer Segments |
| `assignable_type` | crm_contacts / crm_companies |
| `quota_period` | annual / quarterly |

## Permissions

- `crm.territory.view`
- `crm.territory.manage-territories`
- `crm.territory.assign-contacts`
- `crm.territory.view-reporting`
- `crm.territory.manage-quotas`

## Filament

- **Resource:** `TerritoryResource`
- **Pages:** `ListTerritories`, `CreateTerritory`, `ViewTerritory` (with contact list and pipeline summary)
- **Custom pages:** `TerritoryReportPage` — pipeline and quota attainment per territory
- **Widgets:** `TerritoryQuotaWidget` — quota attainment % per rep on CRM dashboard
- **Nav group:** Contacts (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Salesforce Territory Management | Enterprise territory assignment |
| HubSpot Teams | Rep-level territory assignment |
| SPOTIO | Field sales territory management |
| MapAnything | Geographic territory mapping |

## Related

- [[contacts]]
- [[deals]]
- [[forecasting]]
- [[revenue-intelligence]]
