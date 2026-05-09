---
type: module
domain: Communications & Internal Comms
panel: comms
phase: 5
status: planned
cssclasses: domain-comms
migration_range: 550500–550999
last_updated: 2026-05-09
---

# Email Integration

Connect personal and shared mailboxes into FlowFlex. Shared inboxes for teams (support@, sales@, accounts@). Email threads linked to CRM contacts, support tickets, and projects.

---

## Personal Email

Connect Gmail / Outlook / IMAP:
- Email visible alongside other FlowFlex notifications
- Send from FlowFlex (using connected account)
- Emails auto-linked to CRM contacts by sender address
- Track email opens and link clicks (CRM touchpoint)

Links to CRM [[email-tracking]] module.

---

## Shared Team Inboxes

Multiple people manage one email address:
| Inbox | Team |
|---|---|
| support@ | Support team |
| sales@ | Sales SDR team |
| accounts@ | Finance / AP team |
| info@ | General enquiries |

Features:
- Assign email to a team member
- Notes/private comments on an email thread (not visible to sender)
- Status: Open / In Progress / Resolved
- Snooze: come back to this in 2 hours
- Collision detection: "Max is replying..." — prevents double replies

---

## Rules & Automation

Auto-routing rules per shared inbox:
- Subject contains "invoice" → assign to Finance
- Sender domain = `enterprise-client.com` → assign to their account manager
- New sender → create CRM contact if not exists
- Auto-reply outside business hours

---

## Thread Context

When viewing an email from a CRM contact:
- Right panel shows: contact record, company, open deals, recent interactions
- "Create CRM note from this email" one click
- "Create support ticket from this email" one click
- "Add to project" link this email to a task

---

## Email Templates

Saved reply templates for common responses:
- Shared templates per team
- Personal templates
- Variable placeholders: `{{contact.first_name}}`

---

## Data Model

### `comms_inboxes`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| email_address | varchar(300) | |
| type | enum | personal/shared |
| team_id | ulid | nullable FK |

### `comms_email_threads`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| inbox_id | ulid | FK |
| subject | varchar(500) | |
| status | enum | open/in_progress/resolved/snoozed |
| assigned_to | ulid | nullable FK |
| crm_contact_id | ulid | nullable FK |

---

## Migration

```
550500_create_comms_inboxes_table
550501_create_comms_email_threads_table
550502_create_comms_email_messages_table
```

---

## Related

- [[MOC_Communications]]
- [[team-messaging]]
- [[MOC_CRM]] — email tracking
