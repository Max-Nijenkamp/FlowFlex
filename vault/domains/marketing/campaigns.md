---
type: module
domain: Marketing
domain-key: marketing
panel: marketing
module-key: marketing.campaigns
status: planned
priority: p3
depends-on: [crm.contacts, crm.segments, core.billing, core.rbac, foundation.queues, foundation.email]
soft-depends: [marketing.utm, marketing.analytics]
fires-events: []
consumes-events: []
patterns: [states, queues, email]
tables: [mkt_campaigns, mkt_campaign_recipients, mkt_unsubscribes]
permission-prefix: marketing.campaigns
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Campaigns

Email marketing campaigns: build, segment, schedule, send, and track. Bulk one-off sends to customer segments.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] + [[domains/crm/customer-segments\|crm.segments]] | recipients via `SegmentService::contacts()` |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] + [[domains/foundation/email-setup\|foundation.email]] | gating, permissions, batched sending |
| Soft | [[domains/marketing/utm-tracking\|marketing.utm]], [[domains/marketing/marketing-analytics\|marketing.analytics]] | tagged links, dashboards |

---

## Core Features

- Campaign record: name, subject, from name/email, audience segment, content, schedule, status
- Status machine: `draft → scheduled → sending → sent` (+ `failed`, resumable *(assumed)*)
- Audience: CRM segments or manual contact lists (recipients snapshot at schedule)
- Email content: rich text (Tiptap) v1; block builder later *(assumed)*
- Personalisation merge fields (`{{first_name}}`…)
- A/B testing: subject-line variants v1, split %, winner by open rate *(assumed)*
- Schedule send or send now
- Batched, rate-limited sending via queue
- Tracking: opens (pixel), clicks (wrapped links), bounces, **unsubscribes — mandatory footer link + suppression list enforced on every marketing send**
- Per-recipient delivery status

---

## Data Model

### mkt_campaigns

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name / subject | string | + subject_b nullable, split_percent (A/B) |
| from_name / from_email | string | |
| segment_id | ulid nullable | or manual list |
| content | text | purified |
| status | string default `draft` | state machine |
| scheduled_at / sent_at | timestamp nullable | |
| deleted_at | timestamp nullable | |

### mkt_campaign_recipients

| Column | Type | Notes |
|---|---|---|
| id, campaign_id FK, company_id (indexed), contact_id FK | ulid | unique `(campaign_id, contact_id)` |
| variant | string nullable | a / b |
| status | string default `pending` | pending/sent/delivered/failed |
| opened_at / clicked_at / bounced_at / unsubscribed_at | timestamp nullable | |

### mkt_unsubscribes — id, company_id (indexed), email (unique per company), unsubscribed_at — shared suppression list (campaigns + sequences)

---

## DTOs

### CreateCampaignData — name, subject (+ subject_b?, split_percent 10–50), from_name/from_email, segment_id or contact_ids[], content (required, purified), scheduled_at? (future)

## Services & Actions

- `CampaignService::schedule(...)` — materialises recipients; suppression list + `email_deliverable=false` excluded
- `SendCampaignBatchJob` — `notifications` queue, chunked, per-recipient try/catch, personalisation, pixel + wrapped links + unsubscribe footer; resume-safe (pending-only)
- `Track{Open,Click}Controller` + `UnsubscribeController` — public token endpoints
- `CampaignService::stats(...)` — funnel per variant

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `DispatchScheduledCampaignsCommand` | default | every 5 min | status transition guard |
| `SendCampaignBatchJob` | notifications | chained | recipient pending-status guard |

---

## Filament

**Nav group:** Campaigns

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CampaignResource` | #1 CRUD resource | composer, audience picker, test-send, stats on view |
| `CampaignStatsWidget` | #6 widget | open/click funnel |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('marketing.campaigns.view-any') && BillingService::hasModule('marketing.campaigns')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Public/portal guard** (HIGH): Specify these endpoints use signed/tokenized URLs (e.g. Laravel signed routes or per-recipient opaque token validation) and run outside the Sanctum session guard; document the token scheme and that they resolve company by token, not session.
- **Rate limiter** (medium): Add a rate limiter (throttle middleware, per-IP or per-token) to the public Track/Unsubscribe routes in the spec.

---

## Permissions

`marketing.campaigns.view-any` · `marketing.campaigns.create` · `marketing.campaigns.send`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Suppressed + undeliverable excluded at materialisation
- [ ] Unsubscribe works without auth, suppresses future sends
- [ ] A/B variants split per %; stats per variant
- [ ] Batch resume sends only pending
- [ ] Open/click update once per recipient
- [ ] Personalisation per recipient

---

## Build Manifest

```
database/migrations/xxxx_create_mkt_campaigns_table.php
database/migrations/xxxx_create_mkt_campaign_recipients_table.php
database/migrations/xxxx_create_mkt_unsubscribes_table.php
app/Models/Marketing/{Campaign,CampaignRecipient,Unsubscribe}.php
app/States/Marketing/Campaign/{CampaignState,Draft,Scheduled,Sending,Sent,Failed}.php
app/Data/Marketing/{CreateCampaignData,CampaignStatsData}.php
app/Services/Marketing/CampaignService.php
app/Providers/Marketing/MarketingServiceProvider.php
app/Jobs/Marketing/SendCampaignBatchJob.php
app/Http/Controllers/{TrackOpenController,TrackClickController,UnsubscribeController}.php
app/Console/Commands/Marketing/DispatchScheduledCampaignsCommand.php
app/Filament/Marketing/Resources/CampaignResource.php
app/Filament/Marketing/Widgets/CampaignStatsWidget.php
database/factories/Marketing/{CampaignFactory,CampaignRecipientFactory}.php
tests/Feature/Marketing/{CampaignSendTest,UnsubscribeTest,AbTestTest}.php
```

---

## Related

- [[domains/marketing/email-sequences]]
- [[domains/crm/customer-segments]]
- [[architecture/email]]
- [[architecture/queue-jobs]]
