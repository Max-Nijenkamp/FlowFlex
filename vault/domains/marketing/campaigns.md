---
type: module
domain: Marketing
panel: marketing
module-key: marketing.campaigns
status: planned
color: "#4ADE80"
---

# Campaigns

Email marketing campaigns: build, segment, schedule, send, and track. Bulk one-off sends to customer segments.

## Core Features

- Campaign record: name, subject, from name/email, audience segment, content, schedule, status
- Status machine: `draft → scheduled → sending → sent` (spatie/laravel-model-states)
- Audience: CRM segments or uploaded lists
- Email builder: drag-and-drop blocks or rich text (Tiptap)
- Personalisation merge fields
- A/B testing: subject line or content variants
- Schedule send or send now
- Batched, rate-limited sending via queue
- Tracking: opens, clicks, bounces, unsubscribes
- Per-recipient delivery status

## Data Model

| Table | Key Columns |
|---|---|
| `mkt_campaigns` | company_id, name, subject, from_name, from_email, segment_id, content, status, scheduled_at, sent_at |
| `mkt_campaign_recipients` | campaign_id, company_id, contact_id, status, opened_at, clicked_at, bounced_at, unsubscribed_at |

## Filament

**Nav group:** Campaigns

- `CampaignResource` — list, create (builder + audience), schedule, view stats
- `CampaignStatsWidget` — open rate, click rate funnel

## Cross-Domain / Jobs

- Batched queue sending (see [[architecture/queue-jobs]])
- Pulls segments from CRM

## Related

- [[domains/marketing/email-sequences]]
- [[domains/crm/customer-segments]]
- [[architecture/email]]
