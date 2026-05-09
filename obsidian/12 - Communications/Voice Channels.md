---
tags: [flowflex, domain/communications, voice, audio, phase/5]
domain: Communications
panel: communications
color: "#0284C7"
status: planned
last_updated: 2026-05-08
---

# Voice Channels

Always-on voice rooms. Team members can drop in and out like an open office — no calls to schedule, no invites to send. The Discord Stage concept applied to business teams.

**Who uses it:** Teams who work together remotely (engineering, support, design, sales floors)
**Filament Panel:** `communications`
**Depends on:** Core, [[Internal Messaging & Chat]], Native WebRTC infrastructure
**Phase:** 5

---

## Features

### Voice Rooms

- Persistent voice rooms (always exist, never need to be started)
- Each room has a name and optional description: "Engineering Standup", "Sales Floor", "Deep Work — no interruptions"
- List of voice rooms shown in sidebar below chat channels
- Green dot shows how many people are currently in each room

### Joining & Leaving

- Click room name → join instantly (no confirmation screen)
- Speaker indicator: pulsing ring appears around active speakers
- Mute/unmute with one click or keyboard shortcut (default: Space bar)
- Leave room: click Leave or close the floating bar
- Persistent HUD: small floating bar at bottom of screen while in a room showing who's connected

### Room Modes

| Mode | Use case |
|---|---|
| **Open** | Anyone in the workspace can join |
| **Team-only** | Only members of specific team/department |
| **Locked** | Host locked it — new joiners go to waiting queue |
| **Stage** | One speaker, others are audience (raise hand to speak) |

### Presence & Status

- When in a voice room, status auto-shows: "🎙 In Engineering Room"
- Duration in room shown to other members
- "Do Not Disturb" mode: prevents auto-join from any channel button

### Screen Share in Voice Rooms

- Any participant can start screen share within a voice room
- Others see the shared screen inline in the HUD or full-screen option
- Useful for async pairing without a formal call

---

## Database Tables (2)

### `voice_rooms`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | string nullable | |
| `mode` | enum | `open`, `team`, `locked`, `stage` |
| `team_id` | ulid FK nullable | if team-only |
| `is_persistent` | boolean default true | |
| `sort_order` | integer | |
| `created_by` | ulid FK | |

### `voice_room_sessions`
| Column | Type | Notes |
|---|---|---|
| `room_id` | ulid FK | |
| `tenant_id` | ulid FK | |
| `joined_at` | timestamp | |
| `left_at` | timestamp nullable | |
| `was_muted` | boolean default false | |

---

## Permissions

```
communications.voice-rooms.view
communications.voice-rooms.join
communications.voice-rooms.create
communications.voice-rooms.manage
communications.voice-rooms.stage-speak
```

---

## Related

- [[Communications Overview]]
- [[Internal Messaging & Chat]]
- [[Native Video Calls]]
