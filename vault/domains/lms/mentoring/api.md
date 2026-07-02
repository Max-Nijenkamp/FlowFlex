---
domain: lms
module: mentoring
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Mentoring — API / DTOs

## `RequestMentorshipData`

| Field | Type | Rules |
|---|---|---|
| `mentor_id` | ulid | must be an accepting mentor, ≠ self |
| `focus_area` | string | required |

## `LogSessionData`

| Field | Type | Rules |
|---|---|---|
| `mentorship_id` | ulid | acting user must be a **participant** |
| `session_date` | date | ≤ today |
| `notes` | text | nullable; pair-only |
| `action_items` | array | nullable |
| `rating` | int | nullable, optional feedback |

## Read APIs

| Query | Output / Scope |
|---|---|
| `MentorDirectoryQuery` | Accepting mentors + expertise (skills-fed when active) |
| Sessions | Scoped to participants only — never returned to non-participants (incl. HR) |

## Endpoints

- No public routes. All surfaces are Filament, scoped to participants (or HR pairings-only for `view-pairings`).
