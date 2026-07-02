---
domain: marketing
module: campaigns
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Campaigns

Email marketing campaigns: build, segment, schedule, send, and track. Bulk one-off sends to CRM customer segments, with A/B subject-line testing, open/click tracking and a mandatory suppression list.

- **module-key:** `marketing.campaigns` · **panel:** marketing · **priority:** p3
- **fires-events:** none · **consumes-events:** none
- **tables:** `mkt_campaigns`, `mkt_campaign_recipients`, `mkt_unsubscribes`

## What it does

- Campaign record: name, subject (+ subject_b/split_percent for A/B), from name/email, audience segment or manual list, content, schedule, status.
- State machine: `draft → scheduled → sending → sent` (+ `failed`, resume-safe). See [[architecture]].
- Audience read from CRM segments via `SegmentService::contacts()`; recipients **snapshotted** at schedule time.
- Rich text content (Tiptap, purified); personalisation merge fields (`{{first_name}}`…).
- A/B testing: subject-line variants, split %, winner by open rate *(assumed)*.
- Batched, rate-limited queue sending; opens (pixel), clicks (wrapped links), bounces tracked per recipient.
- **Unsubscribe is mandatory**: every marketing send carries a footer link; the `mkt_unsubscribes` suppression list is enforced at materialisation and shared with [[../email-sequences/_module|Email Sequences]].

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|crm.contacts]] + [[../../crm/customer-segments/_module\|crm.segments]] | recipients via `SegmentService::contacts()` (read-only) |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions, `canAccess()` |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | batched sending |
| Hard | [[../../foundation/email-setup/_module\|foundation.email]] | outbound transport |
| Soft | [[../utm-tracking/_module\|marketing.utm]] | tagged links |
| Soft | [[../marketing-analytics/_module\|marketing.analytics]] | dashboards |

## Sibling notes

- [[architecture]] — services, jobs, state machine, Filament artifacts
- [[data-model]] — three tables + ERD
- [[api]] — `CreateCampaignData`, `CampaignStatsData` DTOs
- [[security]] — public token endpoints, suppression, rate limits
- [[decisions]] · [[unknowns]]
- [[features/compose-schedule]] · [[features/audience-materialisation]] · [[features/ab-testing]] · [[features/tracking-suppression]]

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `SegmentService::contacts()` | [[../../crm/customer-segments/_module\|crm.segments]] | audience (read-only) |
| Reads | contact deliverability flag | [[../../crm/contacts/_module\|crm.contacts]] | exclude undeliverable |
| Public | Track/Unsubscribe token endpoints | (guest) | signed/opaque tokens, outside session guard |

No cross-domain **domain events** fired or consumed (see [[../../../architecture/event-bus]]).

**Data ownership:** `marketing.campaigns` writes **only** `mkt_campaigns`, `mkt_campaign_recipients`, `mkt_unsubscribes`. Audiences are **read** from CRM via `SegmentService`; it never writes CRM tables. Cross-domain effects flow via read APIs only ([[../../../security/data-ownership]]).

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

## Related

- [[../email-sequences/_module|Email Sequences]] · [[../marketing-analytics/_module|Marketing Analytics]]
- [[../../crm/customer-segments/_module|Segments]] · [[../../../architecture/email]] · [[../../../architecture/queue-jobs]]
