---
type: module
domain: HR & People
panel: hr
module-key: hr.feedback
status: planned
color: "#4ADE80"
---

# Employee Feedback

Continuous lightweight feedback between employees and managers — real-time recognition, coaching notes, and 1-on-1 records. Complements formal performance reviews.

## Core Features

- Feedback record: from, to, type (praise/constructive/note), message, visibility
- Feedback types: praise (public recognition), constructive (private), coaching note
- 1-on-1 meeting records: agenda, notes, action items, date
- Recognition feed: public praise visible to the team
- Request feedback: ask a colleague/manager for feedback
- Feedback linked to goals or performance review cycles
- Manager dashboard: feedback given/received per report
- Tags via spatie/laravel-tags (e.g. skill, value demonstrated)

## Data Model

| Table | Key Columns |
|---|---|
| `hr_feedback` | company_id, from_employee_id, to_employee_id, type, message, visibility (public/private), related_goal_id |
| `hr_one_on_ones` | company_id, manager_id, employee_id, meeting_date, agenda, notes, action_items (json) |

## Filament

**Nav group:** Performance

- `FeedbackResource` — give/view feedback, recognition feed
- `OneOnOneResource` — log 1-on-1 meetings, track action items
- `RecognitionFeedPage` (custom page) — team praise wall

## Cross-Domain

- Feeds into [[domains/hr/performance-reviews]] cycles
- Recognition notifications via Core Notifications

## Related

- [[domains/hr/performance-reviews]]
- [[domains/hr/employee-profiles]]
