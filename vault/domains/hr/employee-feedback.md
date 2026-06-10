---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.feedback
status: planned
priority: v1
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: [hr.performance]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [hr_feedback, hr_one_on_ones]
permission-prefix: hr.feedback
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Employee Feedback

Continuous lightweight feedback between employees and managers — real-time recognition, coaching notes, and 1-on-1 records. Complements formal performance reviews.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | feedback between employees |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, recognition notifications |
| Soft | [[domains/hr/performance-reviews\|hr.performance]] | feedback surfaces in review context; standalone otherwise |

---

## Core Features

- Feedback record: from, to, type (praise/constructive/note), message, visibility
- Feedback types: praise (public recognition), constructive (private), coaching note (manager-only visibility)
- 1-on-1 meeting records: agenda, notes, action items, date
- Recognition feed: public praise visible to the team
- Request feedback: ask a colleague/manager for feedback
- Feedback linked to goals or performance review cycles
- Manager dashboard: feedback given/received per report
- Tags via spatie/laravel-tags (e.g. skill, value demonstrated)

---

## Data Model

### hr_feedback

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| from_employee_id / to_employee_id | ulid FK | from ≠ to |
| type | string | praise / constructive / coaching-note |
| message | text | plain text *(assumed — no rich text)* |
| visibility | string | public / private — forced: praise=public-capable, constructive=private, coaching-note=manager-chain *(assumed)* |
| related_goal_id | ulid nullable FK hr_review_goals | |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, to_employee_id)`, `(company_id, visibility, created_at)` (feed)

### hr_one_on_ones

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| manager_id / employee_id | ulid FK | |
| meeting_date | date | |
| agenda / notes | text nullable | visible to the two participants only |
| action_items | jsonb | [{title, done}] |
| deleted_at | timestamp nullable | |

---

## DTOs

### GiveFeedbackData — to_employee_id (required, ≠ self), type (in set), message (required, max:2000), visibility (consistent with type — cross-field), related_goal_id (nullable), tags[]
### LogOneOnOneData — employee_id (must report to current manager *(assumed)*), meeting_date, agenda, notes, action_items[]

Message: "Constructive feedback is always private."

## Services & Actions

Actions (simple ops):
- `GiveFeedbackAction::run(GiveFeedbackData $data): Feedback` — notifies recipient; public praise also lands on feed
- `RequestFeedbackAction::run(string $fromEmployeeId): void` — notification asking for feedback
- `LogOneOnOneAction::run(LogOneOnOneData $data): OneOnOne`

**Visibility rules enforced in query scopes**: private feedback readable by sender + recipient (+ HR `view-any`); coaching notes by manager chain + HR; 1-on-1s by the two participants only.

---

## Filament

**Nav group:** Performance

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `FeedbackResource` | #1 CRUD resource | give/view; visibility-scoped queries |
| `OneOnOneResource` | #1 CRUD resource | participant-scoped; action-item checklist |
| `RecognitionFeedPage` | #3-style custom page (feed) | public praise wall; polling 60s |

---

## Permissions

`hr.feedback.view-any` (HR) · `hr.feedback.give` · `hr.feedback.view-own` · `hr.feedback.one-on-one`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Visibility: private feedback invisible to third employees; coaching note invisible to recipient's peers; 1-on-1 visible to participants only
- [ ] Praise lands on public feed; constructive never does
- [ ] Self-feedback rejected
- [ ] Recipient notified on feedback + request
- [ ] Tags attach/filter (spatie/laravel-tags company-scoped *(assumed: tags table scoped via team/tenant convention)*)

---

## Build Manifest

```
database/migrations/xxxx_create_hr_feedback_table.php
database/migrations/xxxx_create_hr_one_on_ones_table.php
app/Models/HR/{Feedback,OneOnOne}.php
app/Data/HR/{GiveFeedbackData,LogOneOnOneData}.php
app/Actions/HR/{GiveFeedbackAction,RequestFeedbackAction,LogOneOnOneAction}.php
app/Filament/HR/Resources/{FeedbackResource,OneOnOneResource}.php
app/Filament/HR/Pages/RecognitionFeedPage.php
database/factories/HR/{FeedbackFactory,OneOnOneFactory}.php
tests/Feature/HR/{FeedbackVisibilityTest,OneOnOneTest}.php
```

---

## Related

- [[domains/hr/performance-reviews]]
- [[domains/hr/employee-profiles]]
- [[architecture/packages]] (`spatie/laravel-tags`)
