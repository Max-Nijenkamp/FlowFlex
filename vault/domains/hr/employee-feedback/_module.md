---
domain: hr
module: employee-feedback
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Employee Feedback

> Rebuild blueprint. HR code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing below is built, shipped, or tested — this describes intended behavior only.

## Purpose

Continuous lightweight feedback between employees and managers — real-time recognition, coaching notes, and 1-on-1 records. Complements formal performance reviews rather than replacing them.

## Intended Behavior

- Feedback records flow between employees: praise (public-capable recognition), constructive (always private), coaching notes (manager-chain visibility).
- Public praise surfaces on a recognition feed visible to the team.
- Employees can request feedback from a colleague or manager.
- Managers keep 1-on-1 meeting records (agenda, notes, action-item checklist) visible only to the two participants.
- Feedback can link to a goal or performance-review cycle, and carry tags (skill / value demonstrated) via `spatie/laravel-tags`.
- Manager dashboard intent: feedback given/received per direct report.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module\|hr.profiles]] | feedback between employees |
| Hard | core.billing + core.rbac + core.notifications | gating, permissions, recognition notifications |
| Soft | [[../performance-reviews/_module\|hr.performance]] | feedback surfaces in review context; standalone otherwise |

## Data Ownership

Owns tables `hr_feedback`, `hr_one_on_ones` ([[data-model]]) — both `company_id`-scoped. Reads `hr_review_goals` (hr.performance) via the optional `related_goal_id` link; writes to no other domain's tables (cross-domain only via events — [[../../../security/data-ownership]]).

## Cross-Domain Edges

| Direction | Event | Counterpart | Effect |
|---|---|---|---|
| Consumes | review context (soft) | hr.performance | feedback links to a goal/cycle; standalone otherwise |
| Fires | recognition notification | core.notifications | recipient/team notified of new public praise |
| Reads | (no event) | hr.profiles | employees + manager chain for feedback/1:1 routing |

---

## Entity & Concern Notes

- [[architecture]] — actions, custom pages, visibility flow
- [[data-model]] — `hr_feedback`, `hr_one_on_ones` + ERD
- [[api]] — DTOs and actions
- [[security]] — permissions, visibility/confidentiality, tenancy
- [[unknowns]] — assumptions and open questions

## Features

- [[features/feedback|Feedback records]]
- [[features/one-on-ones|1-on-1 meetings]]
- [[features/feedback-requests|Feedback requests]]
- [[features/recognition-feed|Recognition feed]]

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

## Related

- [[../performance-reviews/_module]] (soft-dep)
- [[../employee-profiles/_module]]
- [[../../../glossary]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]
