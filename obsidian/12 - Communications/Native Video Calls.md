---
tags: [flowflex, domain/communications, video, webrtc, phase/5]
domain: Communications
panel: communications
color: "#0284C7"
status: planned
last_updated: 2026-05-08
---

# Native Video Calls

Browser-based video calling built into FlowFlex. No Zoom subscription needed for internal calls. 1:1 calls, team calls, and screen sharing — all from within the platform.

**Who uses it:** All employees (internal), external participants (via link invite)
**Filament Panel:** `communications`
**Depends on:** Core, [[Internal Messaging & Chat]], [[File Storage]] (recording storage)
**Phase:** 5
**Build complexity:** Very High — WebRTC, TURN/STUN servers, recording pipeline

---

## Features

### 1:1 Calls

- Start a call directly from a DM conversation (one click)
- Incoming call notification (in-app + browser notification)
- Call states: ringing → connected → ended
- Video on/off toggle
- Mute/unmute
- Screen sharing (tab, window, or full screen)
- End call button

### Group Calls (up to 16 participants)

- Create a meeting from any channel or project
- Invite by: channel members, individual users, or shareable link
- Participant grid layout (speaker-focused view when someone talks)
- Raise hand
- Reactions (👍 🎉 🔥 — appear briefly on participant tile)
- Waiting room: host admits participants before starting
- Co-host assignment
- Remove participant (host/co-host only)

### Screen Sharing

- Share: entire screen, specific window, browser tab
- Request to share (participant can ask host permission)
- Annotation tools over shared screen (pointer, draw, highlight)
- Remote control: host can grant control of their screen to a participant

### Recording

- One-click recording (host starts, all participants notified)
- Recording stored to S3 via FileStorageService
- Auto-generates: video file (MP4), transcript (AI speech-to-text), chapter markers
- Meeting summary: AI-generated 3-5 bullet points of key discussion points
- Action items extracted: AI identifies "we need to..." and "X will..." statements → creates tasks
- Recording shared to: meeting chat, linked project, or meeting notes

### Meeting Notes

- Collaborative notes panel (open during call)
- Notes auto-saved to call record
- Shared after call with all participants

### Calendar Integration

- "Start call" button on scheduled meetings from [[Booking & Appointment Scheduling]]
- Meeting link embedded in calendar invites
- Join from email invite (no FlowFlex account required for external guests)
- 5-minute early join (enter lobby before host)

### External Guest Access

- One-time join link (no login required)
- Guest sets display name before joining
- Waiting room always on for external guests
- Guest permissions: no recording, no screen annotation

---

## Technical Architecture

### WebRTC Stack

- **Signalling:** Laravel WebSockets (via Pusher/Soketi)
- **STUN:** Google STUN servers (stun.l.google.com) for public IP discovery
- **TURN:** Coturn self-hosted for relay when direct P2P fails (NATed networks, corporate firewalls)
- **SFU (Selective Forwarding Unit):** MediaSoup or LiveKit for group calls (5+ participants)
- **Recording:** MediaSoup recording to S3 in real-time segments, assembled post-call

### Call Quality

- Adaptive bitrate based on connection quality
- Echo cancellation and noise suppression (browser-native WebRTC)
- Network quality indicator (excellent/good/poor) shown per participant

---

## Database Tables (4)

### `video_calls`
| Column | Type | Notes |
|---|---|---|
| `type` | enum | `direct`, `group`, `meeting` |
| `host_id` | ulid FK | → tenants |
| `channel_id` | ulid FK nullable | → chat_channels |
| `room_key` | string unique | WebRTC room identifier |
| `started_at` | timestamp nullable | |
| `ended_at` | timestamp nullable | |
| `duration_seconds` | integer nullable | |
| `recording_file_id` | ulid FK nullable | → files |
| `recording_transcript` | text nullable | AI transcript |
| `ai_summary` | text nullable | AI meeting summary |
| `participant_count` | integer | |
| `status` | enum | `waiting`, `active`, `ended` |

### `video_call_participants`
| Column | Type | Notes |
|---|---|---|
| `call_id` | ulid FK | |
| `tenant_id` | ulid FK nullable | null for guests |
| `guest_name` | string nullable | |
| `joined_at` | timestamp | |
| `left_at` | timestamp nullable | |
| `was_host` | boolean | |
| `was_co_host` | boolean | |

### `video_call_action_items`
| Column | Type | Notes |
|---|---|---|
| `call_id` | ulid FK | |
| `text` | text | extracted action |
| `assigned_to_id` | ulid FK nullable | → tenants |
| `task_id` | ulid FK nullable | if converted to task |
| `extracted_at` | timestamp | |

### `video_call_notes`
| Column | Type | Notes |
|---|---|---|
| `call_id` | ulid FK | |
| `author_id` | ulid FK nullable | |
| `content` | json | block editor |
| `updated_at` | timestamp | |

---

## Permissions

```
communications.video-calls.start
communications.video-calls.join
communications.video-calls.record
communications.video-calls.view-recordings
communications.video-calls.manage
```

---

## Competitor Comparison

| Feature | FlowFlex | Zoom | Google Meet | Microsoft Teams |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€14/mo) | ✅ (with Google) | ✅ (with M365) |
| AI meeting summary + actions | ✅ | ✅ (AI add-on) | ✅ | ✅ (Copilot €€) |
| Auto-creates tasks from call | ✅ | ❌ | ❌ | ❌ |
| Integrated with platform chat | ✅ | ❌ | ❌ | ✅ |
| Linked to projects/records | ✅ | ❌ | ❌ | ❌ |
| External guest access | ✅ | ✅ | ✅ | ✅ |
| 16-person group calls | ✅ | ✅ | ✅ | ✅ |

---

## Related

- [[Communications Overview]]
- [[Internal Messaging & Chat]]
- [[Booking & Appointment Scheduling]]
- [[Meeting & Video Integration]]
- [[Task Management]]
