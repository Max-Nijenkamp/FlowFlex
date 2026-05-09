---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 3
status: planned
migration_range: 250000–299999
last_updated: 2026-05-09
---

# Email Tracking & Send from CRM

Track opens, clicks, and replies on individual sales/support emails sent from CRM. Log all email activity against contacts and deals. Replaces HubSpot email tracking, Salesforce Inbox, Yesware, Mixmax.

**Panel:** `crm`  
**Phase:** 3 — core CRM activity tracking

---

## Features

### Send Email from CRM
- Compose and send emails from contact/deal record without leaving CRM
- From address: connected personal email (Gmail/Outlook OAuth), shared inbox, or company alias
- Rich text editor: formatting, attachments, inline images, merge tags
- Templates: personal templates + shared team templates
- Schedule send: "send at 9am tomorrow"
- Follow-up reminder: "remind me if no reply in 3 days"

### Open Tracking
- 1px tracking pixel auto-inserted on send (configurable opt-out)
- First open and every subsequent open logged with timestamp + approximate location
- "Viewed" notification: browser/push notification when prospect opens email
- Open count shown on email activity in CRM
- GDPR note: tracking pixel must be disclosed in email footer (auto-added)

### Click Tracking
- All links in email replaced with tracked redirect
- Per-link click count and timestamps
- Which link got most clicks (useful for proposals with multiple CTAs)
- Link click triggers workflow (e.g. clicked pricing page → alert sales rep)

### Reply Detection
- Incoming reply auto-matched to original sent email
- Reply logged as activity on contact/deal
- Thread view on contact record (full email thread, both sent and received)

### Email Activity Feed
- Per contact: full timeline of all emails (sent, received, opened, clicked)
- Per deal: same, scoped to deal participants
- Filter by: sent by me / sent by team / inbound / outbound

### Email Performance (Rep-Level)
- Open rate, reply rate, click rate per rep per period
- Best-performing templates (open rate, reply rate)
- Best send time (when does this contact typically open?)
- Bounce and unsubscribe tracking

### Shared Inbox Integration
- Inbound emails to shared inbox (sales@, support@) auto-create/update CRM activity
- Assignment rules: auto-assign to rep based on domain, lead score, round-robin
- See conversation history across team (no "who replied last?" confusion)

---

## Data Model

```erDiagram
    crm_emails {
        ulid id PK
        ulid company_id FK
        ulid sent_by FK
        ulid contact_id FK
        ulid deal_id FK
        string subject
        text body_html
        string status
        string message_id
        integer open_count
        integer click_count
        boolean replied
        timestamp sent_at
        timestamp first_opened_at
        timestamp replied_at
    }

    crm_email_events {
        ulid id PK
        ulid email_id FK
        string event_type
        string ip_address
        string user_agent
        string link_url
        timestamp occurred_at
    }
```

---

## Permissions

```
crm.email.send
crm.email.view-own-tracking
crm.email.view-team-tracking
crm.email.manage-templates
```

---

## Related

- [[MOC_CRM]]
- [[entity-contact]]
- [[MOC_Marketing]] — bulk email is Marketing; individual tracked emails are CRM
