---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.activities
status: planned
color: "#4ADE80"
---

# Activities

Calls, emails, meetings, and tasks logged against contacts and deals. The activity log is the source of truth for all customer interactions.

---

## Core Features

- Activity types: Call, Email, Meeting, Task, Note
- Log against: contact, deal, account — appears on all three timelines
- Activity date/time, duration, outcome, description
- Task completion: mark task done, set follow-up reminder
- Activity due date + reminder notification via Core Notifications
- Activity feed: chronological timeline on contact/deal view pages
- Filter by type, owner, date range
- Overdue task detection and dashboard badge

---

## Data Model

| Table | Key Columns |
|---|---|
| `crm_activities` | company_id, type (call/email/meeting/task/note), subject, description, owner_id, contact_id, deal_id, account_id, activity_date, duration_minutes, outcome, is_complete, due_at |

---

## Filament

**Nav group:** Activities

- `ActivityResource` — list (filter by type/owner/status), create, edit, complete action
- Activity timeline embedded in Contact/Deal/Account view pages as a relatable widget

---

## Related

- [[domains/crm/contacts]]
- [[domains/crm/deals]]
