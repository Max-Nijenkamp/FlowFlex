---
type: moc
domain: Communications & Internal Comms
panel: comms
cssclasses: domain-comms
phase: 5
color: "#7C3AED"
last_updated: 2026-05-08
---

# Communications & Internal Comms — Map of Content

Internal chat, announcements, video meetings, intranet, booking, native video, voice, async video, and external chat widget.

**Panel:** `comms`  
**Phase:** 5  
**Migration Range:** `650000–699999`  
**Colour:** Violet-600 `#7C3AED` / Light: `#EDE9FE`  
**Icon:** `heroicon-o-chat-bubble-left-right`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| [[team-messaging\|Team Messaging]] | 5 | planned | Channels, DMs, threads, reactions, file sharing |
| [[company-announcements\|Company Announcements]] | 5 | planned | Broadcast posts, acknowledgement tracking, targeting |
| [[video-meeting-integration\|Video Meeting Integration]] | 5 | planned | Zoom/Meet/Teams links, recording, AI summary, action items |
| [[email-integration\|Email Integration]] | 5 | planned | Personal + shared inboxes, collision detection, CRM sync |
| [[knowledge-base-wiki\|Knowledge Base & Wiki]] | 5 | planned | Block editor, SOPs, handbooks, AI search |
| Company Intranet | 5 | planned | Internal pages, news feed, org links, shortcuts |
| Booking & Appointment Scheduling | 5 | planned | External booking pages, calendar sync, reminders |
| Native Video Calls | 5 | planned | Browser WebRTC video, screen share, recording |
| Voice Channels | 5 | planned | Voice rooms (Discord-style), persistent open channels |
| Async Video Messaging | 5 | planned | Loom-style screen+cam recording, share links |
| External Chat Widget | 5 | planned | Embeddable live chat widget, agent routing |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `MeetingScheduled` | Booking | Notifications (all attendees) |
| `ChatMessageReceived` | Internal Chat | Notifications (user mentioned) |
| `BookingConfirmed` | Booking | CRM (create/update contact), Notifications |
| `SupportChatStarted` | External Chat Widget | CRM (create ticket) |

---

## Permissions Prefix

`comms.chat.*` · `comms.announcements.*` · `comms.booking.*`  
`comms.video.*` · `comms.intranet.*` · `comms.widget.*`

---

## Competitors Displaced

Slack · Microsoft Teams · Loom · Calendly · Intercom (chat widget) · Notion (intranet)

---

## Related

- [[MOC_Domains]]
- [[MOC_CRM]] — external chat → support tickets
- [[MOC_HR]] — announcements, intranet for employee comms
- [[MOC_Projects]] — meeting scheduling around project milestones
