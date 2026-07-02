---
domain: communications
module: broadcast
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Broadcast

Send announcements and bulk messages to employee groups or customer segments across channels (email, WhatsApp, SMS, in-app), scheduled and rate-limited.

> Sends **through the channel drivers** (inbox-registered); owns only the broadcast + recipient snapshot tables. Audiences are read from CRM/HR via their services.

## Module-key

| Field | Value |
|---|---|
| key | `comms.broadcast` |
| priority | p2 |
| panel | comms |
| permission-prefix | `comms.broadcast` |
| tables | `comms_broadcasts`, `comms_broadcast_recipients` |
| patterns | queues, states |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../shared-inbox/_module\|comms.inbox]] | sends via the channel drivers |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions, `canAccess()` |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | batched, rate-limited sending |
| Soft | [[../../crm/customer-segments/_module\|crm.segments]] | customer audiences via `SegmentService::contacts()` |
| Soft | [[../../hr/employee-profiles/_module\|hr.profiles]] | employee-group audiences |
| Soft | comms.whatsapp / comms.sms / [[../../core/notifications/_module\|core.notifications]] | non-email channels; manual list + email always available |

## Core Features

- Broadcast record: title, channel, audience, body, schedule, status.
- Audience: employee groups (HR departments), CRM segments, or a manual recipient list.
- Channels: email, WhatsApp (approved template only), SMS, in-app notification.
- Schedule: send now or schedule for later.
- Personalisation: `{{first_name}}` substitution per recipient.
- Delivery tracking: sent / delivered / opened (email) / failed counts.
- Queue-batched sending, rate-limited per channel (chunk ~100/min per channel *(assumed)*).
- State: `draft → scheduled → sending → sent | failed`.
- Preview before send (rendered with a sample recipient).
- SMS opt-outs + WhatsApp template rules enforced per channel driver.

## See features/

- [[features/compose-schedule|Compose & Schedule]] — build a broadcast, preview, schedule or send now.
- [[features/recipient-materialisation|Recipient Materialisation]] — snapshot recipients at schedule time (dedupe, opt-outs, undeliverable excluded).
- [[features/delivery-tracking|Delivery Tracking]] — per-recipient status + funnel from channel callbacks.

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Recipient materialisation: segment/employee/manual; dedupe; opt-outs + undeliverable excluded.
- [ ] Personalisation per recipient.
- [ ] Batch failure mid-send: resume sends only pending (no doubles).
- [ ] WhatsApp broadcast requires an approved template.
- [ ] Scheduled dispatch fires once.
- [ ] Funnel counts match recipient statuses.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `SegmentService::contacts()` | [[../../crm/customer-segments/_module\|crm.segments]] | customer audience (read-only) |
| Reads | employee-group query | [[../../hr/employee-profiles/_module\|hr.profiles]] | employee audience (read-only) |
| Reads | `OptOutService::isOptedOut` | [[../sms-channel/_module\|comms.sms]] | exclude opted-out numbers |
| Sends via | `ChannelDriver::send` | [[../shared-inbox/_module\|comms.inbox]] + channel modules | driver contract; inbox owns messages |

No cross-domain **domain events** fired or consumed (see [[../../../architecture/event-bus]]).

**Data ownership:** `comms.broadcast` writes **only** `comms_broadcasts` and `comms_broadcast_recipients` (its own recipient snapshot). Audiences are **read** from CRM/HR via their services; sends route through channel drivers. It never writes CRM, HR, or inbox tables directly ([[../../../security/data-ownership]]).

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../shared-inbox/_module|Shared Inbox]] · [[../../crm/customer-segments/_module|Segments]] · [[../../../architecture/queue-jobs]]
