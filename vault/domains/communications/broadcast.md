---
type: module
domain: Communications
panel: comms
module-key: comms.broadcast
status: planned
color: "#4ADE80"
---

# Broadcast

Send announcements and bulk messages to employee groups or customer segments across channels (email, WhatsApp, SMS, in-app).

## Core Features

- Broadcast record: title, channel, audience, message body, schedule, status
- Audience selection: employee groups (HR), CRM segments, or manual recipient list
- Channels: email, WhatsApp (template), SMS, in-app notification
- Schedule: send now or schedule for later
- Personalisation: `{{first_name}}` variable substitution per recipient
- Delivery tracking: sent, delivered, opened (email), failed counts
- Send via queue in batches (rate-limited per channel)
- Draft, scheduled, sending, sent status
- Preview before send

## Data Model

| Table | Key Columns |
|---|---|
| `comms_broadcasts` | company_id, title, channel, audience_type, audience_config (json), body, status, scheduled_at, sent_at, created_by |
| `comms_broadcast_recipients` | broadcast_id, company_id, recipient_id, recipient_type, status (pending/sent/delivered/opened/failed), sent_at |

## Filament

**Nav group:** Broadcast

- `BroadcastResource` — list, create (audience builder + composer), schedule, view delivery stats
- `BroadcastStatsWidget` — delivery funnel per broadcast

## Cross-Domain / Jobs

- Sending runs as a queued, batched job (see [[architecture/queue-jobs]])
- Pulls audiences from HR (employee groups) and CRM (segments)

## Related

- [[domains/communications/shared-inbox]]
- [[domains/crm/customer-segments]]
- [[domains/hr/employee-profiles]]
