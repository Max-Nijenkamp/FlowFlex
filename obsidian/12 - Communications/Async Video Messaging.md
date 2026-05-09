---
tags: [flowflex, domain/communications, async-video, loom, phase/5]
domain: Communications
panel: communications
color: "#0284C7"
status: planned
last_updated: 2026-05-08
---

# Async Video Messaging

Record a video update and share it with your team. The Loom alternative built into FlowFlex. No switching apps, no extra subscription — just record, share, and move on.

**Who uses it:** All employees
**Filament Panel:** `communications`
**Depends on:** Core, [[File Storage]], [[Internal Messaging & Chat]]
**Phase:** 5

---

## Features

### Recording Options

- **Screen + camera** (bubble overlay of speaker in corner)
- **Screen only** (no camera)
- **Camera only** (talking head — updates, feedback, personal messages)
- Microphone required; camera optional
- Quality: up to 1080p, configurable (lower for faster upload)
- Max recording length: 30 minutes (configurable per plan)

### Recording Interface

- Browser-based recording (no extension required; uses MediaRecorder API)
- Countdown 3-2-1 before recording starts
- Clear recording indicator (red dot + timer)
- Pause and resume during recording
- Retake: discard and start again before publishing

### Sharing

- Auto-generates shareable link immediately after recording completes
- Share to: chat channel, DM, project, task comment, email
- Embed as rich link: shows thumbnail, duration, preview on hover
- Password-protect option (for sensitive updates)
- Expiry: optional link expiry date

### Viewer Experience

- In-app player (no external site)
- Speed controls: 0.75x / 1x / 1.25x / 1.5x / 2x
- Chapters (auto-generated from screen/topic changes, or manually set by creator)
- AI-generated transcript (shown alongside video, click to jump to timestamp)
- Reactions: 👍 ❤️ 😂 🔥 — shown on timeline
- Comments: timestamped text comments visible on timeline + below video

### AI Features

- Auto-title: AI suggests a title based on spoken content (first 10 seconds)
- Transcript: full AI speech-to-text
- Summary: 3-5 bullet point summary
- Action items: AI extracts commitments → offered as tasks

### Analytics (Creator View)

- View count and unique viewers
- Completion rate: what % watched to end
- Per-viewer: who watched, when, how much
- Engagement timeline: where reactions and comments happened

---

## Database Tables (3)

### `video_messages`
| Column | Type | Notes |
|---|---|---|
| `creator_id` | ulid FK | → tenants |
| `title` | string nullable | |
| `description` | text nullable | |
| `duration_seconds` | integer | |
| `file_id` | ulid FK | → files (the video) |
| `thumbnail_file_id` | ulid FK nullable | |
| `transcript` | text nullable | AI transcript |
| `ai_summary` | text nullable | |
| `recording_type` | enum | `screen_camera`, `screen`, `camera` |
| `view_count` | integer default 0 | |
| `status` | enum | `processing`, `ready`, `error` |
| `password_hash` | string nullable | |
| `expires_at` | timestamp nullable | |

### `video_message_views`
| Column | Type | Notes |
|---|---|---|
| `video_id` | ulid FK | |
| `viewer_id` | ulid FK nullable | null for anonymous |
| `watched_seconds` | integer | |
| `watched_at` | timestamp | |

### `video_message_comments`
| Column | Type | Notes |
|---|---|---|
| `video_id` | ulid FK | |
| `author_id` | ulid FK | |
| `timestamp_seconds` | integer nullable | if pinned to timeline |
| `body` | text | |

---

## Permissions

```
communications.video-messages.create
communications.video-messages.view
communications.video-messages.delete-own
communications.video-messages.delete-any
communications.video-messages.view-analytics
```

---

## Competitor Comparison

| Feature | FlowFlex | Loom | Loom for Teams | Vidyard |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€8-15/user/mo) | ❌ | ❌ |
| AI transcript + summary | ✅ | ✅ | ✅ | ✅ |
| Auto-creates tasks from video | ✅ | ❌ | ❌ | ❌ |
| Integrated with platform chat | ✅ | partial (Slack plugin) | partial | ❌ |
| Viewer analytics | ✅ | ✅ | ✅ | ✅ |
| Timestamped comments | ✅ | ✅ | ✅ | ✅ |

---

## Related

- [[Communications Overview]]
- [[Internal Messaging & Chat]]
- [[Native Video Calls]]
- [[Task Management]]
