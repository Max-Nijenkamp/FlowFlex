---
domain: hr
module: performance-reviews
feature: self-and-manager-reviews
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Self & Manager Reviews (360)

Intended, not built. See [[../_module]].

## Purpose

The review submissions themselves: self-assessment, manager review, and peer (360) review, plus HR calibration.

## Behavior

- Review types: `self`, `manager`, `peer`. On activation, self + manager rows are auto-generated per active employee; peers are chosen by the manager *(assumed)*.
- A reviewer submits via `SubmitReviewData` (rating in scale, per-question content); only their own assigned review, only while the cycle is `active` — else `NotYourReviewException` / `ReviewLockedException`.
- HR calibrates ratings during the `calibration` state (`CalibrateRatingData`, note required on change *(assumed)*, audited).
- Surfaced via `ReviewResource`: own pending reviews + submit form; HR sees all.
- Peer reviewer identity is never shown to the reviewee *(assumed)*.

## Tables

`hr_reviews` (owner).

## Permissions

`hr.performance.submit` (submit own), `hr.performance.calibrate` (HR calibration), `hr.performance.view` / `view-any`. Visibility matrix in [[../security]].

## UI

- **Kind**: custom-page (side-by-side self vs manager review)
- **Page**: "Reviews" (`/hr/reviews`) + review submit/compare view (`/hr/reviews/{review}`)
- **Layout**: reviewer sees own pending reviews + a submit form (rating in scale, per-question content); calibration view presents self and manager reviews side-by-side for HR; peer reviewer identity hidden from the reviewee *(assumed)*
- **Key interactions**: reviewer submits their own assigned review while cycle is `active`; HR calibrates ratings (`CalibrateRatingData`, note required on change *(assumed)*, audited)
- **States**: empty (no assigned reviews → "Nothing to review") · loading (form save) · error (`NotYourReviewException` / `ReviewLockedException`) · selected (a review in submit or side-by-side calibration mode)
- **Gating**: visible with `hr.performance.view`; submitting own review requires `hr.performance.submit`; calibration requires `hr.performance.calibrate`

## Data

- Owns / writes: `hr_reviews`
- Reads: `hr_review_cycles` (state, rating scale) — own module
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: `review-completed` *(assumed)* → surfaces in hr.feedback review context (per index `performance -.feeds.-> feedback`)
- Shared entity: `hr_employees` + manager chain (hr.profiles) for reviewer assignment

## Test Checklist

### Unit
- [ ] On activation, self + manager rows auto-generate per active employee; peers added by manager
- [ ] `SubmitReviewData` validates rating within the cycle's scale

### Feature (Pest)
- [ ] Reviewer submits own assigned review while cycle is `active`; another reviewer's review → `NotYourReviewException`
- [ ] Submit after lock → `ReviewLockedException`; HR calibrates ratings only in `calibration` state

### Livewire
- [ ] Submit action gated by `hr.performance.submit`; calibration side-by-side view gated by `hr.performance.calibrate`
- [ ] Peer reviewer identity is never shown to the reviewee *(assumed)*

Back to [[../_module]].
