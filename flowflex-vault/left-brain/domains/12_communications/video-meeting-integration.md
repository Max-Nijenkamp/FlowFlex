---
type: module
domain: Communications & Internal Comms
panel: comms
phase: 5
status: planned
cssclasses: domain-comms
migration_range: 552000–552499
last_updated: 2026-05-09
---

# Video Meeting Integration

Connect Zoom, Google Meet, and Microsoft Teams into FlowFlex workflows. Auto-generate meeting links, record meetings, and create action items from transcripts.

---

## Supported Platforms

| Platform | Integration |
|---|---|
| Zoom | OAuth + API |
| Google Meet | Google Workspace API |
| Microsoft Teams | Microsoft Graph API |
| Webex | Cisco API |

---

## Meeting Link Generation

Wherever a meeting is created in FlowFlex, auto-generate a video link:
- **CRM**: schedule a discovery call → Zoom link auto-created + added to calendar invite
- **HR**: book interview → Google Meet link in invite
- **ITSM helpdesk**: escalate ticket to call → Teams link
- **Project**: team standup → recurring Meet link

No more "can you send me the link?" messages.

---

## Calendar Integration

Meetings synced with:
- Google Calendar
- Outlook / Exchange
- Apple Calendar

FlowFlex meeting → appears in personal calendar. Calendar event → appears in FlowFlex.

---

## Meeting Recordings

On meeting end:
- Recording auto-uploaded from Zoom/Teams to FlowFlex storage
- Linked to the CRM contact / project / support ticket the meeting was for
- Access controlled: only meeting participants + admins

---

## Transcription & AI Summaries

Post-meeting:
- Auto-transcript (AI transcription)
- AI summary: key discussion points, decisions made
- Auto-extracted action items → created as tasks in FlowFlex
- Sent to all participants via email + in-app notification

---

## Meeting Analytics

For managers and HR:
- Meeting hours per person per week
- % of time in meetings vs focus time
- Meeting frequency between teams

---

## Data Model

### `comms_meeting_links`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| platform | enum | zoom/meet/teams/webex |
| meeting_id | varchar(200) | platform meeting ID |
| join_url | varchar(500) | |
| entity_type | varchar(50) | crm_activity/it_ticket/etc |
| entity_id | ulid | |
| scheduled_at | timestamp | nullable |
| recording_url | varchar(500) | nullable |

---

## Migration

```
552000_create_comms_meeting_links_table
552001_create_comms_meeting_transcripts_table
```

---

## Related

- [[MOC_Communications]]
- [[team-messaging]]
- [[email-integration]]
- [[MOC_CRM]] — sales call recording
