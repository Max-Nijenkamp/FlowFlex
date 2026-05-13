---
type: module
domain: Events Management
panel: events
phase: 5
status: complete
cssclasses: domain-events
migration_range: 991000–991499
last_updated: 2026-05-12
---

# Attendee Management

Full lifecycle management of attendees: profile data, communication, segmentation, and post-event follow-up. The CRM layer for your event.

---

## Attendee Profiles

Centralised attendee record (persists across events):
- Contact details, company, role, LinkedIn
- Registration history: all past events attended
- Tags: VIP, speaker, sponsor, press, staff
- Communication preferences (email, SMS)
- Custom fields from registration forms

**CRM sync**: attendee profiles synced to/from main CRM contact record. Event attendance becomes a contact activity (touchpoint for sales).

---

## Communication

Automated email sequences per event:
| Trigger | Email |
|---|---|
| Registration | Confirmation + e-ticket |
| T-7 days | Practical info (venue, schedule) |
| T-1 day | Reminder + last-minute logistics |
| During event | "Now live" / session link for virtual |
| Post-event | Survey + recording access + next event |

All emails use event branding. Personalised with attendee name, ticket type, assigned sessions.

SMS notifications: opt-in, used for check-in reminders and real-time session alerts.

---

## Segmented Messaging

Send targeted emails to subsets:
- All attendees / waitlist only / cancelled registrations
- By ticket type (VIP get separate briefing)
- By session registration (session reminder only to registered attendees)
- By company (group booking manager gets separate instructions)

---

## Attendee Portal

Self-service portal for registered attendees:
- View/edit registration details
- Download e-ticket / add to Apple Wallet / Google Wallet
- Build personal agenda (select sessions)
- Transfer or cancel ticket
- Access event resources (pre-reading, sponsor materials)

---

## Data Model

### `evt_attendees`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| crm_contact_id | ulid | nullable FK |
| email | varchar(300) | |
| first_name | varchar(100) | |
| last_name | varchar(100) | |
| company | varchar(200) | nullable |
| job_title | varchar(200) | nullable |
| tags | json | |

### `evt_communications`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| event_id | ulid | FK |
| trigger_type | enum | registration/reminder/post_event/manual |
| subject | varchar(300) | |
| sent_at | timestamp | nullable |
| recipient_count | int | |
| open_rate | decimal(5,2) | nullable |

---

## Migration

```
991000_create_evt_attendees_table
991001_create_evt_communications_table
991002_create_evt_attendee_sessions_table
```

---

## Related

- [[MOC_Events]]
- [[registration-ticketing]]
- [[event-checkin-app]]
- [[post-event-analytics]]
- [[MOC_CRM]] — contact sync
