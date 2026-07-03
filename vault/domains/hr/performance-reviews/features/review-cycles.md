---
domain: hr
module: performance-reviews
feature: review-cycles
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Review Cycles

Intended, not built. See [[../_module]].

## Purpose

Configurable review cycles that scope a round of reviews across the company.

## Behavior

- Frequency configurable: annual, bi-annual, quarterly (`type`).
- Each cycle carries a period (`period_start`/`period_end`) and a rating scale (labels, default 1–5 *(assumed)*).
- Cycle lifecycle is driven by the state machine `draft → active → calibration → finalised` (see [[../architecture]]).
- Activation generates the review matrix (self + manager per active employee; peers by manager); throws `EmptyCycleException` when there are no active employees.
- Managed via `ReviewCycleResource` (CRUD) with a completion-% column and activate/calibrate/finalise actions.

## Tables

`hr_review_cycles` (owner), `hr_reviews` (generated on activation).

## Permissions

`hr.performance.manage-cycles` (create/activate), `hr.performance.calibrate` (advance to finalised), `hr.performance.view-any`.

## UI

- **Kind**: simple-resource
- **Page**: "Review Cycles" (`/hr/review-cycles`)
- **Layout**: Filament table (name, type, period, status badge, completion-% column) + create/edit form for frequency and rating scale; activate/calibrate/finalise row actions
- **Key interactions**: HR creates a cycle, activates it (generates the self+manager review matrix), advances through calibration to finalised
- **States**: empty (no cycles → "Create your first review cycle") · loading (table skeleton) · error (`EmptyCycleException` on activation with no active employees) · selected (cycle row with stage actions)
- **Gating**: visible with `hr.performance.view-any`; create/activate requires `hr.performance.manage-cycles`; finalise requires `hr.performance.calibrate`

## Data

- Owns / writes: `hr_review_cycles`, `hr_reviews` (matrix generated on activation)
- Reads: active-employee list + manager hierarchy via hr.profiles/org
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: `review-completed` *(assumed)* → could surface in hr.feedback review context (per index `performance -.feeds.-> feedback`)
- Shared entity: `hr_employees` + manager chain (hr.profiles / org)

## Test Checklist

### Unit
- [ ] Activation with no active employees throws `EmptyCycleException`
- [ ] Review matrix = one self + one manager row per active employee (peers added by manager)

### Feature (Pest)
- [ ] `activateCycle` generates the review rows and sends due notifications
- [ ] Cycle advances `draft → active → calibration → finalised`; company A cannot touch company B cycles

### Livewire
- [ ] Create/activate denied without `hr.performance.manage-cycles`; finalise denied without `hr.performance.calibrate`
- [ ] Completion-% column reflects submitted/total reviews

Back to [[../_module]].
