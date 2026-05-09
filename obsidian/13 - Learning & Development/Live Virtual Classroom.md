---
tags: [flowflex, domain/lms, virtual-classroom, live-training, phase/7]
domain: Learning & Development
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-08
---

# Live Virtual Classroom

Run instructor-led training sessions online without leaving FlowFlex. Scheduled classes, breakout rooms, polls, whiteboards, hand-raise — the full classroom experience in a browser tab. Replaces Zoom Webinars + your LMS juggling.

**Who uses it:** L&D managers, instructors, all employees as learners
**Filament Panel:** `lms` (admin + instructor view); Vue + Inertia (learner view)
**Depends on:** Core, [[Course Builder & LMS]], [[Native Video Calls]], [[File Storage]]
**Phase:** 7

---

## Features

### Scheduling

- Create a live session linked to a course or as standalone
- Recurrence: one-time or repeating (weekly, bi-weekly)
- Max capacity: set enrolment cap
- Waitlist: auto-admit from waitlist when someone cancels
- Calendar invite: auto-send .ics to all enrolled learners
- Reminder notifications: 24h and 15min before session

### Classroom Interface

- Video grid: instructor + up to 30 participants (SFU, same WebRTC stack as video calls)
- Instructor view: participant list, hand queue, chat, Q&A panel, attendance tracker
- Learner view: raise hand, chat, submit question anonymously
- Screen sharing: instructor shares slides or screen
- Virtual whiteboard: collaborative drawing canvas (excalidraw-style embedded)
- Breakout rooms: split learners into smaller groups for exercises (host assigns or random)

### Engagement Tools

- Polls: instructor launches real-time multiple choice poll, results shown live
- Quiz mid-session: auto-scores, results feed into course completion record
- Reactions: 👍 🤔 😂 — shown briefly on participant tile
- Spotlight mode: pin a participant's video
- Attendance: auto-tracked (joined/left timestamps)

### Recording & Post-Session

- One-click recording (same pipeline as [[Native Video Calls]])
- Recording auto-attached to the course module for asynchronous catch-up
- AI transcript and chapter markers generated post-session
- AI-generated session summary sent to all participants
- Attendance report: who attended, for how long

### Instructor Tools

- Slide presenter mode: upload PDF or PowerPoint → displayed in-session
- Timer: visible countdown for exercises
- Lock room: stop new joiners mid-session
- Mute all: one-click silence all participants
- Remove/admit participants
- Private chat with co-instructor

---

## Database Tables (3)

### `lms_live_sessions`
| Column | Type | Notes |
|---|---|---|
| `course_id` | ulid FK nullable | |
| `instructor_id` | ulid FK | |
| `title` | string | |
| `description` | text nullable | |
| `scheduled_at` | timestamp | |
| `duration_minutes` | integer | |
| `capacity` | integer nullable | |
| `recording_file_id` | ulid FK nullable | |
| `transcript` | text nullable | |
| `ai_summary` | text nullable | |
| `status` | enum | `scheduled`, `live`, `ended`, `cancelled` |
| `video_call_id` | ulid FK nullable | → video_calls |

### `lms_session_attendances`
| Column | Type | Notes |
|---|---|---|
| `session_id` | ulid FK | |
| `learner_id` | ulid FK | |
| `joined_at` | timestamp nullable | |
| `left_at` | timestamp nullable | |
| `duration_minutes` | integer nullable | |
| `attended` | boolean | |

### `lms_session_polls`
| Column | Type | Notes |
|---|---|---|
| `session_id` | ulid FK | |
| `question` | string | |
| `options` | json | string[] |
| `results` | json | {option: count} |
| `launched_at` | timestamp | |
| `closed_at` | timestamp nullable | |

---

## Permissions

```
lms.live-sessions.create
lms.live-sessions.host
lms.live-sessions.join
lms.live-sessions.view-recordings
lms.live-sessions.manage
```

---

## Competitor Comparison

| Feature | FlowFlex | Zoom Webinars | GoTo Training | Adobe Connect |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€149+/mo) | ❌ (€109+/mo) | ❌ |
| Integrated with LMS progress | ✅ | ❌ | partial | ❌ |
| AI transcript + summary | ✅ | ✅ (AI add-on) | ❌ | ❌ |
| Breakout rooms | ✅ | ✅ | ✅ | ✅ |
| Mid-session quiz tied to course | ✅ | ❌ | ✅ | ✅ |
| Attendance auto-linked to record | ✅ | partial | ✅ | ✅ |

---

## Related

- [[LMS Overview]]
- [[Course Builder & LMS]]
- [[Native Video Calls]]
- [[External Learner Portal]]
