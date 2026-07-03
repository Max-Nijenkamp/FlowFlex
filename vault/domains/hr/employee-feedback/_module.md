---
domain: hr
module: employee-feedback
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Employee Feedback

Continuous lightweight feedback between employees and managers — real-time recognition, coaching notes, and 1-on-1 records. Complements formal performance reviews rather than replacing them.

> Rebuild blueprint. HR code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing below is built, shipped, or tested — this describes intended behavior only.

---

## Module-key

`hr.feedback`

**Priority:** v1
**Panel:** hr
**Permission prefix:** `hr.feedback`
**Tables:** `hr_feedback`, `hr_one_on_ones`
**Nav group:** Performance

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module\|hr.profiles]] | feedback between employees |
| Hard | core.billing + core.rbac + core.notifications | gating, permissions, recognition notifications |
| Soft | [[../performance-reviews/_module\|hr.performance]] | feedback surfaces in review context; standalone otherwise |

---

## Core Features

- Feedback records — praise (public-capable recognition), constructive (always private), coaching notes (manager-chain visibility) — [[features/feedback|Feedback records]]
- Public praise surfaces on a team-visible recognition feed — [[features/recognition-feed|Recognition feed]]
- Employees can request feedback from a colleague or manager — [[features/feedback-requests|Feedback requests]]
- Managers keep 1-on-1 meeting records (agenda, notes, action-item checklist) visible only to the two participants — [[features/one-on-ones|1-on-1 meetings]]
- Feedback can link to a goal or performance-review cycle, and carry tags (skill / value demonstrated) via `spatie/laravel-tags`
- Manager dashboard intent: feedback given/received per direct report

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

Filament artifacts (resources, recognition feed page) and per-write-path concurrency tiers: [[architecture]].

---

## Test Checklist

- [ ] Tenant isolation: company A cannot read company B feedback or 1-on-1 records
- [ ] Module gating: artifacts hidden when `hr.feedback` inactive
- [ ] Visibility forced by type: constructive is private, praise is public-capable, coaching notes follow the manager chain — enforced in query scope, not just UI
- [ ] Public praise appears on the recognition feed; constructive/coaching never do
- [ ] Self-feedback (`from == to`) rejected
- [ ] 1-on-1 agenda/notes readable only by the two participants — HR `view-any` cannot read them
- [ ] Give/request actions notify recipient/target and are rate-limited (`panel-action`, comms)

---

## Cross-Domain Edges

| Direction | Event | Counterpart | Effect |
|---|---|---|---|
| Consumes | review context (soft) | hr.performance | feedback links to a goal/cycle; standalone otherwise |
| Fires | recognition notification | core.notifications | recipient/team notified of new public praise |
| Reads | (no event) | hr.profiles | employees + manager chain for feedback/1:1 routing |

**Data ownership:** owns `hr_feedback`, `hr_one_on_ones` ([[data-model]]) — both `company_id`-scoped. Reads `hr_review_goals` (hr.performance) via the optional `related_goal_id` link; writes to no other domain's tables (cross-domain only via events — [[../../../security/data-ownership]]).

---

## Related

- Entity notes: [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[unknowns]]
- [[../performance-reviews/_module]] (soft-dep)
- [[../employee-profiles/_module]]
- [[../../../glossary]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]
