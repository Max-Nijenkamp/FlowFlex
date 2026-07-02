---
domain: it
module: access-provisioning
feature: access-review-matrix
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Access Review Matrix

A periodic audit view: an employees × systems grid showing who has access to what, with an exportable
snapshot for compliance reviews.

- Custom Filament page ([[../../../../architecture/patterns/custom-pages]]) backed by `AccessReviewQuery::matrix()`.
- Rows = employees, columns = systems, cells = current grant status / access level.
- Export action produces a downloadable snapshot — **throttled per company-user**.

## UI

- **Kind**: custom-page — employees × systems matrix.
- **Page**: `AccessReviewPage` at `/it/access-review`.
- **Layout**: grid with employee rows and system columns; each cell shows the access level or an empty marker; header export button; optional filters (system, access level).
- **Key interactions**: scan the matrix for over/under-provisioning; **Export** → throttled snapshot download (`RateLimiter` keyed on `company_id:user_id`).
- **States**: empty (no grants → "nothing to review" message) · loading (skeleton grid) · error (export throttled → toast "try again shortly") · selected (row/column highlight on hover).
- **Gating**: `it.access.view-any`; `canAccess()` stated explicitly on the custom page (per [[../../../../architecture/filament-patterns]] #1).

## Data

- Owns / writes: nothing — read-only over `it_access_grants` (+ `it_systems` for columns).
- Reads: `it_access_grants`, `it_systems`; employee reference from hr.profiles.
- Cross-domain writes: none — read-only; cross-domain effects flow through events only, never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing (reads current grant state).
- Feeds: nothing — audit view only.
- Shared entity: employees owned by hr.profiles (read only); grants from [[access-grants]]; systems from [[system-catalogue]].

## Unknowns

- Matrix build via `AccessReviewQuery::matrix()` in one query (no N+1) — `*(assumed)*`.
- Export throttle limit (requests / window) — `*(assumed)*`.

## Related

- [[../_module|Access Provisioning]] · [[access-grants]] · [[system-catalogue]] · [[../security|security]] · [[../../../../architecture/patterns/custom-pages]]
