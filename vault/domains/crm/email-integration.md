---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.email
status: planned
color: "#4ADE80"
---

# Email Integration

Bi-directional email sync with Gmail/Outlook. Emails auto-linked to contacts and deals. Send tracked emails from within CRM.

## Core Features

- OAuth connection: Gmail / Outlook per user
- Inbound sync: received emails matched to contacts by email address
- Outbound: send email from CRM, logged against the contact/deal
- Email tracking: open tracking (pixel), link click tracking
- Email templates with merge fields
- Conversation thread view on contact/deal record
- Shared vs private email visibility
- Auto-log: emails appear in the activity timeline

## Data Model

| Table | Key Columns |
|---|---|
| `crm_email_connections` | company_id, user_id, provider, oauth_token (encrypted), email_address, sync_enabled |
| `crm_emails` | company_id, contact_id, deal_id, direction, subject, body, sent_at, opened_at, clicked_at, message_id |

## Filament

**Nav group:** Activities

- `EmailConnectionResource` — connect/manage mailbox (per user)
- Email thread shown on Contact + Deal view pages
- Compose + send action from contact/deal

## Cross-Domain / Security

- OAuth tokens encrypted (see [[architecture/patterns/encryption]])
- Inbound sync via scheduled job or provider webhook

## Related

- [[domains/crm/contacts]]
- [[domains/crm/activities]]
- [[architecture/patterns/encryption]]
