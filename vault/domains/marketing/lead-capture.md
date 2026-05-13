---
type: module
domain: Marketing
panel: marketing
module-key: marketing.leads
status: planned
color: "#4ADE80"
---

# Lead Capture

> Capture, score, and route inbound leads from forms, landing pages, and integrations directly into the CRM pipeline.

**Panel:** `marketing`
**Module key:** `marketing.leads`

## What It Does

Lead Capture is the bridge between marketing activity and the sales pipeline. It provides an embeddable form builder, a lead scoring engine that grades each incoming lead, and routing rules that automatically assign leads to the right sales rep or queue. Every form submission becomes a CRM contact, enriched with the source campaign, UTM parameters, and a score. Sales reps see only warm, routed leads rather than a raw unfiltered firehose.

## Features

### Core
- Form builder: multi-step forms with text, email, phone, select, checkbox, and hidden fields
- Embed options: JavaScript snippet for any website, native use inside landing pages, or standalone hosted form URL
- Submission to CRM: each submission creates or updates a CRM contact with field mapping configured per form
- Source tracking: UTM parameters captured automatically and stored on the lead record
- Duplicate detection: match incoming email against existing contacts before creating a new record
- GDPR consent checkbox with configurable consent text, mandatory before submit

### Advanced
- Lead scoring: point-based model (title = 10, company size >50 = 15, downloaded ebook = 5, visited pricing page = 20)
- Score thresholds: hot (80+), warm (40–79), cold (<40) — colour-coded in lead inbox
- Routing rules: assign leads to rep or queue based on score, geography, company size, product interest
- Lead nurture trigger: cold leads auto-enrolled in drip email sequence
- Round-robin assignment: distribute leads evenly across a sales team
- Real-time notifications: Slack or email alert to assigned rep the moment a hot lead submits

### AI-Powered
- Company enrichment: auto-fill company name, industry, size, and website from email domain
- Intent scoring boost: increase score if visitor also viewed pricing or demo pages before submitting

## Data Model

```erDiagram
    mkt_lead_forms {
        ulid id PK
        ulid company_id FK
        string name
        json fields
        ulid campaign_id FK
        json routing_rules
        boolean requires_gdpr_consent
        string consent_text
        timestamps timestamps
    }

    mkt_leads {
        ulid id PK
        ulid company_id FK
        ulid form_id FK
        ulid contact_id FK
        json field_values
        string source_utm_campaign
        string source_utm_medium
        string source_utm_source
        integer score
        string temperature
        ulid assigned_to FK
        string status
        timestamp submitted_at
        timestamps timestamps
    }

    mkt_lead_score_rules {
        ulid id PK
        ulid company_id FK
        string attribute
        string operator
        string value
        integer points
        boolean is_active
        timestamps timestamps
    }

    mkt_lead_forms ||--o{ mkt_leads : "captures"
    mkt_lead_score_rules }o--|| mkt_leads : "scores"
```

| Table | Purpose |
|---|---|
| `mkt_lead_forms` | Form configuration and routing rules |
| `mkt_leads` | Individual lead records with score and assignment |
| `mkt_lead_score_rules` | Point rules for lead scoring engine |

## Permissions

```
marketing.leads.view-any
marketing.leads.view-own
marketing.leads.manage-forms
marketing.leads.manage-scoring
marketing.leads.export
```

## Filament

**Resource class:** `LeadResource`
**Pages:** List, View
**Custom pages:** `LeadFormBuilderPage` (form designer), `ScoringRulesPage` (score rule management)
**Widgets:** `LeadInboxWidget` (hot leads assigned to current user), `LeadVolumeWidget` (daily submission trend)
**Nav group:** Content

## Displaces

| Competitor | Feature Replaced |
|---|---|
| HubSpot Forms + Lead Scoring | Form builder and point-based scoring |
| Marketo Lead Management | Scoring, routing, and nurture triggers |
| Typeform + Zapier | Form capture and CRM routing |
| Clearbit Reveal | Company enrichment on form submit |

## Related

- [[landing-pages]] — forms embedded in landing pages
- [[email-marketing]] — cold leads enrolled in drip sequences
- [[campaigns]] — leads tagged to originating campaign
- [[analytics]] — lead volume and conversion funnel
- [[../crm/INDEX]] — leads become CRM contacts and opportunities
