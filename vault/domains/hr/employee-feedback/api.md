---
domain: hr
module: employee-feedback
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Employee Feedback — DTOs & Services

Intended interface. No cross-domain events fired or consumed.

## DTOs

### GiveFeedbackData
- `to_employee_id` — required, ≠ self
- `type` — in set (praise / constructive / coaching-note)
- `message` — required, max:2000
- `visibility` — consistent with type (cross-field rule)
- `related_goal_id` — nullable
- `tags[]`

Validation message: *"Constructive feedback is always private."*

### LogOneOnOneData
- `employee_id` — must report to current manager *(assumed)*
- `meeting_date`
- `agenda`
- `notes`
- `action_items[]`

## Actions

- `GiveFeedbackAction::run(GiveFeedbackData $data): Feedback` — notifies recipient; public praise also lands on feed.
- `RequestFeedbackAction::run(string $fromEmployeeId): void` — notification asking for feedback.
- `LogOneOnOneAction::run(LogOneOnOneData $data): OneOnOne`.

See [[architecture]] for flow and [[security]] for visibility scopes.
