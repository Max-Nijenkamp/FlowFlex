---
type: module
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
status: complete
migration_range: 480014
last_updated: 2026-05-12
right_brain_log: "[[builder-log-lms-phase7]]"
---

# Live Virtual Classroom

Instructor-led live learning sessions with WebRTC video, breakout rooms, interactive polls, hand-raise queue, session recording, and AI-generated transcripts and summaries.

**Panel:** `lms`  
**Phase:** 7  
**Migration range:** `747500–749999`

---

## Features

### Core (MVP)

- Live session scheduling: create sessions linked to a course or standalone
- Learner registration and waiting list
- WebRTC video room (LiveKit or 100ms integration)
- Presenter controls: mute all, spotlight speaker, screen share
- Chat: public + private messages
- Session recording: auto-record, stored and accessible post-session
- Attendance tracking: join/leave timestamps per learner
- Session completion: mark attended = completes the live lesson in the course

### Advanced

- Breakout rooms: auto or manual assignment, timer, return all
- Interactive polls and quizzes: live Q&A with real-time results
- Hand-raise queue: structured speaking order
- Whiteboard: collaborative drawing canvas
- Pre-session materials: send resources to registered learners
- Post-session survey: automatic satisfaction rating

### AI-Powered

- Auto-transcript: speech-to-text of full session
- AI session summary: key topics discussed, action items, questions raised
- Highlight reel: AI-extracted top moments from recording

---

## Data Model

```erDiagram
    live_sessions {
        ulid id PK
        ulid company_id FK
        ulid course_id FK
        string title
        string instructor_id FK
        timestamp starts_at
        integer duration_minutes
        integer max_attendees
        string room_id
        string recording_url
        string transcript_url
        string ai_summary
        string status
    }

    live_session_attendees {
        ulid id PK
        ulid session_id FK
        ulid learner_id FK
        timestamp joined_at
        timestamp left_at
        boolean completed
    }

    live_sessions ||--o{ live_session_attendees : "has"
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `LiveSessionStarted` | Session goes live | Notifications (registered learners) |
| `LiveSessionCompleted` | Session ends | LMS (mark attendance, update course progress) |
| `LiveSessionRecordingReady` | Recording processed | Notifications (attendees get link) |

---

## Permissions

```
lms.live-sessions.create
lms.live-sessions.host
lms.live-sessions.attend
lms.live-sessions.view-recordings
lms.live-sessions.view-reports
```

---

## Competitors Displaced

Zoom Webinars · Webex Training · Adobe Connect · GoToTraining · BigBlueButton

---

## Related

- [[MOC_LMS]]
- [[course-builder-lms]] — live sessions can be lessons inside a course
- [[entity-employee]]
- [[MOC_Communications]] — WebRTC infrastructure shared with video meetings
