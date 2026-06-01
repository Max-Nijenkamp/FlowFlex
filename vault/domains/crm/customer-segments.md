---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.segments
status: planned
color: "#4ADE80"
---

# Customer Segments

Dynamic contact segments based on attributes and behaviour. Used to target campaigns, assign playbooks, and filter lists.

## Core Features

- Segment builder: filter conditions on contact/account attributes
- Conditions: industry, size, lifecycle stage, deal value, last activity, tag, location, custom fields
- Dynamic membership: contacts auto-enter/exit as data changes
- Static lists: manually curated contact lists (alternative to dynamic)
- Segment size preview before saving
- Use in: Marketing campaigns, CRM sequences, broadcasts
- Combine conditions with AND/OR logic
- Segment overlap analysis

## Data Model

| Table | Key Columns |
|---|---|
| `crm_segments` | company_id, name, type (dynamic/static), conditions (json), member_count |
| `crm_segment_members` | segment_id, company_id, contact_id (for static lists) |

## Filament

**Nav group:** Contacts

- `SegmentResource` — build segment (condition builder), preview size
- Segment used as audience picker in Marketing + Communications

## Cross-Domain

- Consumed by [[domains/marketing/campaigns]], [[domains/communications/broadcast]], CRM sequences

## Related

- [[domains/crm/contacts]]
- [[domains/marketing/campaigns]]
- `spatie/laravel-tags`, `spatie/laravel-schemaless-attributes`
