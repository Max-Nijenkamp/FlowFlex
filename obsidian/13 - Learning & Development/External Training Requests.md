---
tags: [flowflex, domain/lms, training-requests, phase/7]
domain: Learning & Development
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-07
---

# External Training Requests

Employees request to attend conferences, courses, and certifications. Managers approve. L&D tracks the budget and captures completions with certificate upload.

**Who uses it:** All employees (request), managers (approve/reject), L&D team (track budget and completions)
**Filament Panel:** `lms`
**Depends on:** [[HR — Employee Profiles]], [[File Storage]]
**Phase:** 7
**Build complexity:** Low — 2 resources, 1 page, 2 tables

---

## Features

- **Training request form** — any employee can submit a request with course name, provider, URL, cost, currency, duration (days), and written justification for why it's relevant to their role
- **Manager approval workflow** — requests start as `pending`; manager reviews and approves or rejects; `reviewed_by` and `reviewed_at` recorded; rejection requires a `rejection_reason`
- **`TrainingRequestApproved` event** — fires when status → `approved`; notifies employee with approval confirmation and next steps
- **`TrainingRequestRejected` event** — fires when status → `rejected`; notifies employee with rejection reason
- **L&D budget tracking** — total approved spend per period tracked across all requests; dashboard widget showing approved spend vs L&D budget ceiling (if configured)
- **Training completion logging** — after attending, employee marks the request as `completed` and uploads a certificate; `training_completions` record created
- **Certificate storage** — completion certificate stored to S3 via FileStorageService; linked via `certificate_file_id` in `training_completions`
- **Knowledge sharing prompt** — on completion, employee is prompted to write a brief note on learnings; optionally shared as an [[Intranet]] news post
- **Skills linkage** — completed external training can be linked to an employee skill in [[Skills Matrix & Gap Analysis]] to update their proficiency level
- **Completion history** — L&D team views all completed external trainings per employee; used in performance reviews and skills gap analysis

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `training_requests`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | requester → tenants |
| `course_name` | string | |
| `provider` | string nullable | |
| `url` | string nullable | |
| `cost` | decimal(10,2) nullable | |
| `currency` | string(3) default 'GBP' | |
| `duration_days` | integer nullable | |
| `justification` | text | |
| `status` | enum | `pending`, `approved`, `rejected`, `completed` |
| `requested_at` | timestamp | |
| `reviewed_by` | ulid FK nullable | → tenants (manager) |
| `reviewed_at` | timestamp nullable | |
| `rejection_reason` | text nullable | |

### `training_completions`
| Column | Type | Notes |
|---|---|---|
| `training_request_id` | ulid FK | → training_requests |
| `tenant_id` | ulid FK | → tenants |
| `completed_at` | timestamp | |
| `certificate_file_id` | ulid FK nullable | → files |
| `notes` | text nullable | learnings writeup |
| `skill_id` | ulid FK nullable | → skills (optional link) |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `TrainingRequestApproved` | `training_request_id`, `tenant_id` | Notification to employee |
| `TrainingRequestRejected` | `training_request_id`, `tenant_id`, `reason` | Notification to employee |

---

## Events Consumed

None — training requests are manually submitted and approved.

---

## Permissions

```
lms.training-requests.view
lms.training-requests.create
lms.training-requests.edit
lms.training-requests.delete
lms.training-requests.approve
lms.training-requests.reject
lms.training-completions.view
lms.training-completions.create
lms.training-budget.view
```

---

## Related

- [[LMS Overview]]
- [[Course Builder & LMS]]
- [[Skills Matrix & Gap Analysis]]
- [[Company Intranet]]
- [[Budgeting & Forecasting]]
