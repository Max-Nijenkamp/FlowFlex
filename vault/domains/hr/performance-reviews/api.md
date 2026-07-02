---
domain: hr
module: performance-reviews
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Performance Reviews — API

Intended DTOs and service surface. Nothing built yet. No cross-domain events fired or consumed. See [[_module]] and [[architecture]].

## DTOs

spatie/laravel-data. Input DTOs validate before hitting the service.

| DTO | Fields | Rules |
|---|---|---|
| `CreateCycleData` | name (required), period_start / period_end, type, rating_scale (optional labels array) | end after start; type in set |
| `SubmitReviewData` | review_id, rating, content (per-question answers) | rating in scale; only own assigned review; only while cycle `active` |
| `CalibrateRatingData` | review_id, rating, note | note required on change *(assumed)* |
| `ReviewData` | output DTO for a review | — |

## Service Methods

`PerformanceServiceInterface` → `PerformanceService` (see [[architecture]] for full behavior/exceptions):

- `activateCycle(string $cycleId): void`
- `submitReview(SubmitReviewData $data): void`
- `calibrate(CalibrateRatingData $data): void`
- `finalise(string $cycleId): void`

## Events

None. `fires-events: []`, `consumes-events: []`.

## Related

- [[architecture]]
- [[security]]
- [[_module]]
