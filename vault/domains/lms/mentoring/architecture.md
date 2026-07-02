---
domain: lms
module: mentoring
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Mentoring — Architecture

## Relationship Lifecycle

```
active → paused → active
active/paused → completed
```

Plain string `status` *(assumed)*. A mentorship is one mentor + one mentee (≠ self), one active pair at a time.

## Services & Actions

| Method | Responsibility |
|---|---|
| `MentoringService::request(RequestMentorshipData)` | Create a pending/active mentorship to an accepting mentor (≠ self, no duplicate active). |
| `MentoringService::accept(mentorship)` | Mentor accepts; notify. |
| `MentoringService::complete(mentorship)` | Close the relationship. |
| `MentorDirectoryQuery` | Accepting mentors with expertise (skills-fed when active). |

### Session visibility

- `lms_mentorship_sessions` is **query-scoped to participants** (the mentor and mentee only). Even users with `lms.mentoring.view-pairings` (HR) see pairings but **never** session notes. This scope is enforced at the query layer, not just the UI.

## Events

None fired or consumed. Notifications go via core.notifications; expertise reads from skills.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `MentorshipResource` | Mentoring | #1 CRUD resource | Own relationships; sessions relation (pair-scoped). |
| `MentorDirectoryPage` | Mentoring | #17 gallery/directory custom page | Browse + request. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.mentoring.participate')
        && BillingService::hasModule('lms.mentoring');
}
```

## Jobs & Scheduling

None.

## Search & Realtime

Directory search over expertise tags. No realtime.
