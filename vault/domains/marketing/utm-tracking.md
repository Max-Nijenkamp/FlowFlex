---
type: module
domain: Marketing
panel: marketing
module-key: marketing.utm
status: planned
color: "#4ADE80"
---

# UTM Tracking

Track campaign attribution via UTM parameters. Capture source/medium/campaign on form submissions and landing page visits; attribute contacts and revenue to channels.

## Core Features

- UTM capture: `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content` from page URLs
- Store first-touch and last-touch UTM per contact
- UTM builder tool: generate tagged URLs for campaigns
- Attribution: link contacts and (via CRM) deals to originating UTM
- Source/medium/campaign breakdown reports
- First-touch vs last-touch attribution models

## Data Model

| Table | Key Columns |
|---|---|
| `mkt_utm_touches` | company_id, contact_id, touch_type (first/last), source, medium, campaign, term, content, landing_url, occurred_at |

## Filament

**Nav group:** Analytics

- `UtmBuilderPage` (custom page) — build tagged URLs
- UTM attribution shown in Marketing Analytics dashboard

## Cross-Domain

- Attribution flows into CRM contact records and revenue reporting

## Related

- [[domains/marketing/marketing-analytics]]
- [[domains/crm/contacts]]
