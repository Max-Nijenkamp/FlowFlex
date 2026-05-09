---
type: module
domain: Marketing & Content
panel: marketing
cssclasses: domain-marketing
phase: 5
status: planned
migration_range: 400000–449999
last_updated: 2026-05-09
---

# UTM Builder & Link Management

Centralised campaign link management. Build UTM-tagged URLs, create branded short links, track click performance. Replaces UTM.io, Rebrandly, Bitly (business), and spreadsheet-based UTM tracking.

**Panel:** `marketing`  
**Phase:** 5

---

## Features

### UTM Builder
- Form: destination URL + UTM source, medium, campaign, term, content
- Auto-suggestions based on past values (avoid typos: "google" not "Google" not "gooogle")
- UTM naming convention enforcer (configure allowed values per parameter — ensures clean GA4 data)
- Preview: generated URL before save
- QR code generation from any UTM link (for print/outdoor)

### Link Shortener
- Short links on company domain: `go.acme.com/spring-sale`
- Or FlowFlex subdomain: `lnk.flowflex.app/xyz`
- Custom slugs or auto-generated
- Link editing: change destination URL without breaking short link (retargeting)
- Link expiry: set expiry date → redirect to fallback URL after

### Campaign Organisation
- Group links by campaign (e.g. "Q2 2026 Email Campaign")
- Team shared — no more "who has the UTM link for the LinkedIn ad?"
- Tags: channel, region, product, team
- Bulk create: upload CSV of URLs with UTM params → generate all links at once

### Click Analytics
- Total clicks, unique clicks per link
- Click timeline (per day/week/month)
- Top referring domains
- Geographic breakdown (country, city)
- Device/browser breakdown
- Conversion tracking: link click → form submission → deal (if UTM passes through to CRM)

### Link Health Monitoring
- Check destination URL is live (daily scan)
- Flag broken links (404 or redirect errors)
- Alert when link expiry is approaching

---

## Data Model

```erDiagram
    campaign_links {
        ulid id PK
        ulid company_id FK
        ulid campaign_id FK
        ulid created_by FK
        string destination_url
        string short_slug
        string utm_source
        string utm_medium
        string utm_campaign
        string utm_term
        string utm_content
        string full_url
        timestamp expires_at
        integer total_clicks
        integer unique_clicks
    }
```

---

## Permissions

```
marketing.links.create
marketing.links.view-analytics
marketing.links.manage-team-links
```

---

## Competitors Displaced

UTM.io · Rebrandly · Bitly Teams · Short.io · Campaign URL Builder (Google)

---

## Related

- [[MOC_Marketing]]
- [[MOC_Analytics]] — UTM data feeds channel attribution in full analytics
