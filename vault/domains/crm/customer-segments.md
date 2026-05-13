---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.segments
status: planned
color: "#4ADE80"
---

# Customer Segments

> Dynamic customer segments based on contact and company attributes — used to target campaigns, trigger sales sequences, and personalise playbooks.

**Panel:** `crm`
**Module key:** `crm.segments`

## What It Does

Customer Segments allows sales and marketing teams to define named cohorts of contacts based on attribute rules — industry, company size, deal stage, location, status, custom fields, or any combination. Segments are dynamic: they re-evaluate membership automatically as contact data changes. A contact added to a segment today satisfies the rules; if their data changes so they no longer qualify, they are removed automatically. Segments are consumed by Sales Sequences (who to enrol) and can be exported for use in Marketing campaigns.

## Features

### Core
- Segment definition: name, description, and a set of filter rules (AND/OR logic)
- Rule types: contact field (status, source, job title, custom field), company field (industry, size, country), deal field (stage, value, close date), activity field (last activity date, email opened)
- Dynamic membership: segment membership recalculated on contact update and on demand
- Member list: view all contacts currently in a segment with their attribute values — sortable and searchable
- Segment size: current member count displayed on segment list

### Advanced
- Nested logic: complex rules with nested AND/OR groups — (Industry = SaaS OR Industry = FinTech) AND Company Size > 50 AND Status = prospect
- Segment overlap: view which contacts are in multiple segments — used to avoid duplicate outreach
- Static snapshots: export a segment as a static list at a point in time (for campaigns where the list must not change mid-campaign)
- Segment as filter: use any saved segment as a filter within other CRM list views (contacts, deals, activities)
- Segment analytics: how segment membership has changed over time — growth chart and churn rate

### AI-Powered
- Segment suggestions: AI analyses deal win patterns and suggests "Ideal Customer Profile" segments — contacts/companies with attributes similar to companies where deals have been won
- Propensity scoring: AI scores each contact's likelihood to convert, and segments can filter on this score (e.g. "High Propensity Prospects" = propensity score > 80)

## Data Model

```erDiagram
    crm_segments {
        ulid id PK
        ulid company_id FK
        string name
        text description
        json rules
        integer member_count
        timestamp last_evaluated_at
        timestamps created_at/updated_at
    }

    crm_segment_contacts {
        ulid segment_id FK
        ulid contact_id FK
        timestamp added_at
    }
```

| Column | Notes |
|---|---|
| `rules` | JSON rule tree — {operator: AND, conditions: [...]} |
| `member_count` | Cached count — updated after each evaluation |
| `crm_segment_contacts` | Materialised membership for fast queries |

## Permissions

- `crm.segments.view`
- `crm.segments.create`
- `crm.segments.edit`
- `crm.segments.delete`
- `crm.segments.export`

## Filament

- **Resource:** `SegmentResource`
- **Pages:** `ListSegments`, `CreateSegment`, `ViewSegment` (with member list and rule builder)
- **Custom pages:** None
- **Widgets:** `SegmentOverviewWidget` — top segments by member count on CRM dashboard
- **Nav group:** Contacts (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| HubSpot Lists | Smart and static contact lists |
| Salesforce Segments | Account and contact segmentation |
| Klaviyo Segments | Dynamic customer segments |
| ActiveCampaign | CRM contact segmentation |

## Related

- [[contacts]]
- [[sales-sequences]]
- [[revenue-intelligence]]
- [[deals]]
