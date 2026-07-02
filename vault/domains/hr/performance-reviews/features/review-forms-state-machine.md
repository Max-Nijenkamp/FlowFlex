---
domain: hr
module: performance-reviews
feature: review-forms-state-machine
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Review Forms & State Machine

Intended, not built. See [[../_module]].

## Purpose

The per-cycle state machine that locks submissions and freezes ratings at the right time.

## Behavior

- Column `hr_review_cycles.status`, class `ReviewCycleState` (spatie/laravel-model-states, [[../../../../architecture/patterns/states]]).
- `draft → active`: review rows generated; due notifications sent.
- `active → calibration`: submissions locked.
- `calibration → finalised`: ratings frozen; PDFs generated; employees can see results.
- Diagram and full transition table in [[../architecture]].

## Tables

`hr_review_cycles.status` drives; affects `hr_reviews` (submission lock, rating freeze).

## Permissions

`hr.performance.manage-cycles` (draft→active, active→calibration), `hr.performance.calibrate` (calibration→finalised).

## UI

- **Kind**: custom-page (review-form fill flow driven by the cycle state machine)
- **Page**: review fill flow within `ReviewResource` (`/hr/reviews/{review}`); state transitions triggered from `ReviewCycleResource`
- **Layout**: the cycle status (`ReviewCycleState`) drives what the reviewer sees — an editable form while `active`, a locked read-only form in `calibration`, a frozen/result view once `finalised`; a stepper reflects draft → active → calibration → finalised
- **Key interactions**: HR advances cycle state (locks submissions, freezes ratings, triggers PDFs); reviewers fill/submit only while `active`
- **States**: empty (draft → no review rows yet) · loading (transition processing, due notifications sent) · error (submitting after lock → `ReviewLockedException`) · selected (a review row in its state-appropriate mode)
- **Gating**: draft→active and active→calibration require `hr.performance.manage-cycles`; calibration→finalised requires `hr.performance.calibrate`

## Data

- Owns / writes: `hr_review_cycles.status` (drives), `hr_reviews` (submission lock, rating freeze)
- Reads: own module tables
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none directly — finalise triggers [[pdf-export]] and freezes ratings for [[self-and-manager-reviews]]
- Shared entity: `hr_employees` (hr.profiles) for reviewer/reviewee routing

Back to [[../_module]].
