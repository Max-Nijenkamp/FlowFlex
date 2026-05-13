---
type: module
domain: Communications
panel: comms
module-key: comms.broadcasts
status: planned
color: "#4ADE80"
---

# Email Broadcasts

> Send bulk emails to company contacts and customers using a template builder — with open, click, and delivery analytics.

**Panel:** `comms`
**Module key:** `comms.broadcasts`

## What It Does

Email Broadcasts handles bulk email communication from the company to its contacts or customers — distinct from the outbound marketing campaigns in [[../marketing/email-marketing]]. Typical use cases include a customer service notice sent to all customers, a newsletter to opted-in subscribers, a product update to all active users, or a business announcement to partner contacts. The template builder produces branded emails, and basic analytics (delivered, opened, clicked) are tracked per broadcast.

## Features

### Core
- Broadcast creation: subject line, from name, from email, template body
- Template builder: drag-and-drop blocks (text, image, button, divider, social links) with brand colour and logo settings
- Recipient list: upload a CSV, select from CRM contact segments, or use a FlowFlex list from [[../marketing/email-marketing]]
- Send and schedule: send immediately or schedule for a future date/time
- Unsubscribe handling: one-click unsubscribe link auto-injected; GDPR and CAN-SPAM compliant; suppression list managed
- Deliverability: SPF/DKIM configuration per sending domain; bounce handling and suppression

### Advanced
- Personalisation tokens: `{{first_name}}`, `{{company_name}}`, and any custom field from the recipient list
- Conditional sections: show/hide content blocks based on recipient attributes
- A/B test subject lines: send to a 20% test split; winner sent to remaining 80% based on open rate
- Segment filtering: apply filters to the recipient list (by country, customer tag, last purchase date)
- Pre-send testing: send a test copy to up to 5 email addresses before broadcasting
- Analytics: delivered count, open rate, click rate, unsubscribe rate; per-link click counts

### AI-Powered
- Subject line suggestions: generate 5 subject line variants optimised for open rate
- Spam score preview: estimate inbox vs spam placement probability before sending

## Data Model

```erDiagram
    comms_broadcasts {
        ulid id PK
        ulid company_id FK
        string subject
        string from_name
        string from_email
        json template_blocks
        string recipient_source
        json recipient_filters
        integer total_recipients
        string status
        timestamp scheduled_at
        timestamp sent_at
        integer delivered
        integer opened
        integer clicked
        integer unsubscribed
        integer bounced
        timestamps timestamps
    }

    comms_broadcast_events {
        ulid id PK
        ulid broadcast_id FK
        string recipient_email
        string event_type
        string link_url
        timestamp occurred_at
    }

    comms_broadcasts ||--o{ comms_broadcast_events : "tracks"
```

| Table | Purpose |
|---|---|
| `comms_broadcasts` | Broadcast configuration and aggregate stats |
| `comms_broadcast_events` | Per-recipient open, click, and unsubscribe events |

## Permissions

```
comms.broadcasts.view-any
comms.broadcasts.create
comms.broadcasts.send
comms.broadcasts.manage-lists
comms.broadcasts.view-analytics
```

## Filament

**Resource class:** `BroadcastResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `BroadcastTemplateBuilderPage` (WYSIWYG email composer), `BroadcastAnalyticsPage`
**Widgets:** `BroadcastSummaryWidget` (last 5 broadcasts with open rates)
**Nav group:** Broadcast

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Mailchimp (internal broadcasts) | Company broadcast emails to contact lists |
| Campaign Monitor | Branded bulk email with basic analytics |
| Brevo (Sendinblue) | Bulk transactional and broadcast emails |
| Constant Contact | Email broadcasts to customer and contact lists |

## Related

- [[../marketing/email-marketing]] — outbound campaign emails managed in the marketing panel
- [[announcements]] — internal employee announcements; broadcasts target external contacts
- [[notification-center]] — delivery failures surface as notifications
- [[../crm/INDEX]] — contact segments used as recipient lists
