---
domain: marketing
module: landing-pages
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Landing Pages — Security

Parent: [[_module]]

Public unauthenticated render surface with user-authored content — XSS and abuse are the concerns.

## Public render guard

- `GET /p/{company-slug}/{page-slug}` resolves company + page by slug (no session). Published only; draft → 404.
- Per-IP throttle on the render + `RecordVisitAction` route (visit-count abuse) — medium ([[../../../architecture/security]]).

## Content purification

All block rich text is purified (ezyang/htmlpurifier) on save; the renderer never emits raw user HTML. Block type + config are validated against `BlockRegistry` at save.

## Permissions

`marketing.landing-pages.view-any` · `marketing.landing-pages.create` · `marketing.landing-pages.update` · `marketing.landing-pages.publish`. Publish is a distinct permission from edit. Resources gate on `canAccess()`.

## Data ownership

Writes only `mkt_landing_pages`. Embedded form submits are owned by [[../forms/_module|Forms]]; media by [[../../core/file-storage/_module|core.files]] — read-only references only ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[architecture]] · [[../../../architecture/security]]
