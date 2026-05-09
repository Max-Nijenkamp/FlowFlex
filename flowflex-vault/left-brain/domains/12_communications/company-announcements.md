---
type: module
domain: Communications & Internal Comms
panel: comms
phase: 5
status: planned
cssclasses: domain-comms
migration_range: 551500–551999
last_updated: 2026-05-09
---

# Company Announcements

Official top-down communications from leadership. All-hands updates, policy changes, company news. Ensures important messages reach everyone — not buried in Slack channels.

---

## Announcement Types

| Type | Example |
|---|---|
| Company news | New product launch, acquisition, office opening |
| Policy update | New expense policy, holiday schedule |
| People update | New hire announcement, promotion, farewell |
| Emergency | Urgent IT outage, weather closure |
| All-hands summary | Video recording + action points from all-hands |

---

## Delivery Channels

Each announcement pushed through:
- **FlowFlex feed**: prominent in-app notification
- **Email**: to all employees or targeted group
- **Team messaging**: auto-posted to #announcements channel
- **Push notification** (mobile app)

Per announcement: choose which channels to push.

---

## Targeting

Send to:
- All employees
- Specific department(s)
- Specific location(s)
- Specific role / level
- Custom group

---

## Acknowledgement Tracking

For important announcements (policy changes, safety notices):
- Employees must click "I have read and understood this"
- Dashboard: % acknowledged, who has not
- Auto-reminder to outstanding employees
- Manager notified if direct reports haven't acknowledged

---

## Scheduled Publishing

Write now, publish later:
- Schedule date/time for publication
- Save as draft for review/approval before publish
- Campaign approach: post now + follow-up reminder 3 days later

---

## Analytics

Per announcement:
- Open rate (email)
- View rate (in-app)
- Acknowledgement rate
- Click-through rate (if announcement has a link/action)

---

## Data Model

### `comms_announcements`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| title | varchar(300) | |
| content | longtext | |
| type | varchar(50) | |
| requires_acknowledgement | boolean | |
| audience | json | targeting config |
| channels | json | delivery channels |
| status | enum | draft/scheduled/published |
| published_at | timestamp | nullable |
| author_id | ulid | FK |

### `comms_acknowledgements`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| announcement_id | ulid | FK |
| employee_id | ulid | FK |
| acknowledged_at | timestamp | |

---

## Migration

```
551500_create_comms_announcements_table
551501_create_comms_acknowledgements_table
```

---

## Related

- [[MOC_Communications]]
- [[team-messaging]]
- [[knowledge-base-wiki]]
