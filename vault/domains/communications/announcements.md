---
type: module
domain: Communications
panel: comms
module-key: comms.announcements
status: planned
color: "#4ADE80"
---

# Announcements

> Publish company-wide announcements to targeted employee groups with acknowledgement tracking and multi-channel delivery.

**Panel:** `comms`
**Module key:** `comms.announcements`

## What It Does

Announcements is the top-down communication tool for official company messages — the kind that should not be buried in a Slack channel or lost in an inbox. Leadership and HR publish announcements for company news, policy changes, people updates, or emergency notices. Each announcement is targeted to the right audience (all staff, a department, a location, or a custom group), delivered through multiple channels simultaneously (in-app feed, email, team channel post), and tracked for read rates. For policy changes, acknowledgement tracking confirms who has read and understood the message.

## Features

### Core
- Announcement creation: title, body (rich text with images and embedded video), type (company news, policy update, people update, emergency, all-hands summary)
- Audience targeting: all employees, specific department(s), specific location(s), specific role/level, or a custom manually selected group
- Multi-channel delivery: in-app notification feed, email (to employee work address), auto-post to configured team channel
- Scheduled publishing: write now, publish at a future date and time
- Draft and approval workflow: save as draft for review; manager or comms admin approves before publish
- Read tracking: track who has opened the in-app notification or email

### Advanced
- Acknowledgement requirement: toggle "requires acknowledgement" — employees must click "I have read and understood this"; unacknowledged employees are flagged
- Acknowledgement dashboard: % acknowledged, list of employees who have not yet acknowledged, auto-reminder to outstanding employees
- Manager escalation: notify a manager if their direct reports have not acknowledged within a configurable number of days
- Translation: publish announcement in multiple languages; employee sees the version matching their profile language
- Pinning: pin an announcement to the top of the in-app notification feed so it stays visible
- Analytics per announcement: open rate (email), view rate (in-app), acknowledgement rate, click-through rate on any included links

### AI-Powered
- Tone check: flag announcements with a tone that may be perceived as alarming or overly formal, with suggestions for adjustment
- Summary generation: produce a two-sentence TLDR for long announcements for busy readers

## Data Model

```erDiagram
    comms_announcements {
        ulid id PK
        ulid company_id FK
        string title
        text content
        string type
        boolean requires_acknowledgement
        json audience_config
        json delivery_channels
        string status
        timestamp scheduled_at
        timestamp published_at
        ulid author_id FK
        timestamps timestamps
    }

    comms_acknowledgements {
        ulid id PK
        ulid announcement_id FK
        ulid employee_id FK
        timestamp acknowledged_at
    }

    comms_announcement_reads {
        ulid id PK
        ulid announcement_id FK
        ulid employee_id FK
        string channel
        timestamp read_at
    }

    comms_announcements ||--o{ comms_acknowledgements : "requires"
    comms_announcements ||--o{ comms_announcement_reads : "tracked in"
```

| Table | Purpose |
|---|---|
| `comms_announcements` | Announcement content, targeting, and delivery |
| `comms_acknowledgements` | Per-employee acknowledgement records |
| `comms_announcement_reads` | Open/read tracking per channel |

## Permissions

```
comms.announcements.view-any
comms.announcements.create
comms.announcements.publish
comms.announcements.manage-acknowledgements
comms.announcements.delete
```

## Filament

**Resource class:** `AnnouncementResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `AcknowledgementDashboardPage` (% acknowledged with employee breakdown)
**Widgets:** `PendingAcknowledgementsWidget` (announcements with outstanding acknowledgements)
**Nav group:** Internal

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Staffbase | Employee communications and announcement platform |
| Workvivo | Company announcements and social intranet |
| Microsoft Teams Posts | Company-wide broadcast messages |
| Slack Announcements | Channel-based company announcements |

## Related

- [[team-channels]] — announcements auto-posted to a designated channel
- [[messaging]] — urgent announcements can also trigger a direct message alert
- [[notification-center]] — announcements surface in the unified notification inbox
- [[../hr/INDEX]] — employee records used for audience targeting
