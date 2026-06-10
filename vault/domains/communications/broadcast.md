---
type: module
domain: Communications
domain-key: communications
panel: comms
module-key: comms.broadcast
status: planned
priority: p2
depends-on: [comms.inbox, core.billing, core.rbac, foundation.queues]
soft-depends: [crm.segments, hr.profiles, comms.whatsapp, comms.sms, core.notifications]
fires-events: []
consumes-events: []
patterns: [queues, states]
tables: [comms_broadcasts, comms_broadcast_recipients]
permission-prefix: comms.broadcast
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Broadcast

Send announcements and bulk messages to employee groups or customer segments across channels (email, WhatsApp, SMS, in-app).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/communications/shared-inbox\|comms.inbox]] | sends via channel drivers |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, batched sending |
| Soft | [[domains/crm/customer-segments\|crm.segments]] | customer audiences via `SegmentService::contacts()` |
| Soft | [[domains/hr/employee-profiles\|hr.profiles]] | employee group audiences |
| Soft | comms.whatsapp / comms.sms / [[domains/core/notifications\|core.notifications]] | non-email channels; manual list + email always available |

---

## Core Features

- Broadcast record: title, channel, audience, message body, schedule, status
- Audience selection: employee groups (HR departments), CRM segments, or manual recipient list
- Channels: email, WhatsApp (approved template only), SMS, in-app notification
- Schedule: send now or schedule for later
- Personalisation: `{{first_name}}` variable substitution per recipient
- Delivery tracking: sent, delivered, opened (email), failed counts
- Send via queue in batches (rate-limited per channel — chunk 100/min per channel *(assumed)*)
- Status: `draft → scheduled → sending → sent | failed`
- Preview before send (rendered with sample recipient)
- SMS opt-outs + WhatsApp template rules enforced per channel driver

---

## Data Model

### comms_broadcasts

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| channel | string | email / whatsapp / sms / in-app |
| audience_type | string | segment / employee-group / manual |
| audience_config | jsonb | segment_id / department_ids / recipient list |
| body | text | purified; template_id for whatsapp |
| status | string default `draft` | state machine |
| scheduled_at / sent_at | timestamp nullable | |
| created_by | ulid FK users | |
| deleted_at | timestamp nullable | |

### comms_broadcast_recipients

| Column | Type | Notes |
|---|---|---|
| id, broadcast_id FK, company_id (indexed) | ulid | |
| recipient_type / recipient_id | string / ulid nullable | contact / employee / manual |
| address | string | email/phone snapshot |
| status | string default `pending` | pending / sent / delivered / opened / failed |
| sent_at | timestamp nullable | |
| error | string nullable | |

Unique `(broadcast_id, address)` — duplicate-recipient guard.

---

## State Machine

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `draft` | `scheduled` | `comms.broadcast.send` | recipients materialised (snapshot) |
| `scheduled` | `sending` | scheduler reaches time / send-now | batched job chain dispatched |
| `sending` | `sent` | all batches done | counts finalised |
| `sending` | `failed` | infrastructure failure | resumable (pending recipients only) |
| `draft`/`scheduled` | cancelled *(assumed)* | creator | |

---

## DTOs

### CreateBroadcastData — title, channel (in set, active), audience_type + audience_config (validated per type), body (required; whatsapp → approved template ref), scheduled_at? (future)

## Services & Actions

- `BroadcastService::schedule(CreateBroadcastData $data): BroadcastData` — materialises recipients at schedule time (dedupe, SMS opt-outs excluded, `email_deliverable=false` excluded)
- `SendBroadcastBatchJob` — `notifications` queue, chunked, per-recipient try/catch + status, personalisation substitution; rate-limited per channel
- `BroadcastService::stats(string $broadcastId): BroadcastStatsData`
- Delivery/open callbacks update recipient status (channel webhooks)

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `DispatchScheduledBroadcastsCommand` | default | every 5 min | status transition guard scheduled→sending |
| `SendBroadcastBatchJob` | notifications | chained | recipient status `pending` guard — resume-safe |

---

## Filament

**Nav group:** Broadcast

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `BroadcastResource` | #1 CRUD resource | audience builder + composer + preview; delivery funnel on view |
| `BroadcastStatsWidget` | #6 widget | funnel per broadcast |

---

## Permissions

`comms.broadcast.view-any` · `comms.broadcast.create` · `comms.broadcast.send`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Recipient materialisation: segment/employee/manual; dedupe; opt-outs + undeliverable excluded
- [ ] Personalisation per recipient
- [ ] Batch failure mid-send: resume sends only pending (no doubles)
- [ ] WhatsApp broadcast requires approved template
- [ ] Scheduled dispatch fires once
- [ ] Funnel counts match recipient statuses

---

## Build Manifest

```
database/migrations/xxxx_create_comms_broadcasts_table.php
database/migrations/xxxx_create_comms_broadcast_recipients_table.php
app/Models/Comms/{Broadcast,BroadcastRecipient}.php
app/States/Comms/Broadcast/{BroadcastState,Draft,Scheduled,Sending,Sent,Failed}.php
app/Data/Comms/{CreateBroadcastData,BroadcastData,BroadcastStatsData}.php
app/Services/Comms/BroadcastService.php
app/Jobs/Comms/SendBroadcastBatchJob.php
app/Console/Commands/Comms/DispatchScheduledBroadcastsCommand.php
app/Filament/Comms/Resources/BroadcastResource.php
app/Filament/Comms/Widgets/BroadcastStatsWidget.php
database/factories/Comms/{BroadcastFactory,BroadcastRecipientFactory}.php
tests/Feature/Comms/{BroadcastAudienceTest,BroadcastSendTest}.php
```

---

## Related

- [[domains/communications/shared-inbox]]
- [[domains/crm/customer-segments]]
- [[domains/hr/employee-profiles]]
- [[architecture/queue-jobs]]
