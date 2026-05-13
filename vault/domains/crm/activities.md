---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.activities
status: planned
color: "#4ADE80"
---

# Activities

> Calls, emails, meetings, demos, and notes logged against contacts and deals — the interaction history and follow-up task queue for every sales rep.

**Panel:** `crm`
**Module key:** `crm.activities`

## What It Does

Activities captures every sales interaction — calls made, emails sent, meetings held, demos given, and notes taken — and links them to the relevant contact and deal. Each activity can have a due date and a completion mark, making it double-duty as a follow-up task queue. The activity feed on contact and deal detail pages shows the full chronological interaction history so any team member can pick up context instantly. Overdue activities surface on the rep's personal dashboard. Activity data feeds into Revenue Intelligence for deal health scoring.

## Features

### Core
- Activity types: call / email / meeting / demo / task / note / follow-up
- Activity fields: type, subject, description, contact, deal, owner, due date, completed flag, outcome (call result, meeting notes)
- Activity feed: chronological list of all activities on a contact or deal detail page
- My activities: personal view — all open/overdue activities assigned to the current user sorted by due date
- Quick log: log a call or note directly from the contact or deal detail page without navigating away

### Advanced
- Activity reminders: notification fired at due date (and T-1h for meetings) — via notification module
- Meeting scheduler: create meeting activities with a calendar invite link (iCal format) sent to contact
- Outcome fields: call outcome (reached / left voicemail / no answer), meeting outcome (positive / neutral / negative), email response status
- Activity templates: predefined activity sequences for common rep workflows (e.g. "Post-Demo Follow-up" = email D+1, call D+3, check-in email D+7)
- Bulk log: log the same activity outcome for multiple contacts at once (e.g. after an event where you met 20 people)

### AI-Powered
- Activity quality scoring: AI analyses the content of logged notes and call outcomes and scores the interaction quality — low-quality logs (single word outcomes, no context) flagged for improvement
- Conversation intelligence: if call recording is integrated, AI transcribes and summarises calls — key points and action items extracted and pre-filled in the activity note

## Data Model

```erDiagram
    crm_activities {
        ulid id PK
        ulid company_id FK
        ulid deal_id FK
        ulid contact_id FK
        ulid owner_id FK
        string type
        string subject
        text description
        timestamp due_at
        timestamp completed_at
        string outcome
        json metadata
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `type` | call / email / meeting / demo / task / note / follow-up |
| `outcome` | Type-specific: reached / left_voicemail / no_answer for calls |
| `metadata` | JSON for type-specific fields (e.g. call duration, meeting location) |

## Permissions

- `crm.activities.create`
- `crm.activities.view-own`
- `crm.activities.view-team`
- `crm.activities.edit-own`
- `crm.activities.view-all`

## Filament

- **Resource:** `ActivityResource`
- **Pages:** `ListActivities`
- **Custom pages:** `MyActivitiesPage` — personal activity queue with overdue highlighting
- **Widgets:** `OverdueActivitiesWidget` — count of overdue activities on CRM dashboard
- **Nav group:** Activities (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| HubSpot Activities | CRM activity logging |
| Salesforce Activities | Task and event management |
| Pipedrive Activities | Sales activity management |
| Close | Built-in calling and activity logging |

## Related

- [[contacts]]
- [[deals]]
- [[email-integration]]
- [[sales-sequences]]
- [[revenue-intelligence]]
