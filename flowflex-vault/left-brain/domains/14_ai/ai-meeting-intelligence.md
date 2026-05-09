---
type: module
domain: AI & Automation
panel: ai
cssclasses: domain-ai
phase: 6
status: planned
migration_range: 750000–799999
last_updated: 2026-05-09
---

# AI Meeting Intelligence

Auto-join meetings, transcribe in real-time, generate structured summaries, extract action items, and sync to CRM/Projects. Replaces Otter.ai, Fireflies, and Fathom.

---

## Features

### Meeting Bot
- Auto-join Google Meet, Zoom, Teams meetings (calendar integration detects meetings)
- Opt-in per meeting or always-on per user preference
- Visual "FlowFlex AI" participant in meeting
- Real-time transcription (in-meeting display option)
- Recording with speaker labels

### Post-Meeting Summary
- Structured summary: context, key decisions, open questions
- Action items extracted (with assignee and due date)
- One-click push action items → Tasks module (Projects)
- One-click push meeting notes → CRM deal timeline
- CRM contact matching (names in transcript → matched to contacts)

### Smart Search
- Full-text search across all meeting transcripts
- Filter by participant, date range, topic
- "Find all meetings where we discussed pricing" — semantic search

### Templates
- Meeting type templates (sales call, kickoff, QBR, 1:1)
- Each template has expected summary structure
- Custom questions ("Was a next step agreed?") auto-answered from transcript

### Privacy & Compliance
- Meeting recording consent notification to all participants
- GDPR: transcript deletion on request
- Per-meeting recording off-switch
- Data residency EU-only option

---

## Data Model

```erDiagram
    meeting_recordings {
        ulid id PK
        ulid company_id FK
        string meeting_platform
        string external_meeting_id
        string title
        timestamp started_at
        integer duration_seconds
        json participants
        string transcript_url
        text summary
        json action_items
        string status
    }

    meeting_action_items {
        ulid id PK
        ulid recording_id FK
        string description
        ulid assigned_to FK
        date due_date
        ulid task_id FK "nullable — if pushed to Projects"
        boolean is_completed
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `MeetingTranscribed` | Processing complete | Notifications (meeting host), CRM (attach to deal/contact) |
| `ActionItemCreated` | AI extracts action | Projects (optional auto-create task) |

---

## Permissions

```
ai.meetings.view-own
ai.meetings.view-any
ai.meetings.configure-bot
ai.meetings.delete
```

---

## Competitors Displaced

Otter.ai · Fireflies · Fathom · Avoma · tl;dv · Gong (transcription part)

---

## Related

- [[MOC_AI]]
- [[MOC_Communications]] — Native Video Calls integrates with this
- [[MOC_CRM]] — meeting notes pushed to deal timeline
- [[MOC_Projects]] — action items pushed to task management
