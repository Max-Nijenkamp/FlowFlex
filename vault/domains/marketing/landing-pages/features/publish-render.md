---
domain: marketing
module: landing-pages
feature: publish-render
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Publish & Public Render

Make a page live and serve it publicly, mobile-responsive and SEO-tagged.

## Behaviour

- `publish` validates all blocks, sets `status=published`, stamps `published_at`.
- Public GET `/p/{company-slug}/{page-slug}` renders the block array (Vue + Inertia); draft → 404.
- SEO meta + OG image emitted in the head; responsive by block construction.

## UI

- **Kind**: public-vue
- **Page**: `/p/{company-slug}/{page-slug}` (Vue + Inertia block renderer, ui-strategy row #16).
- **Layout**: stacked responsive blocks per the saved array; embedded form block renders the live form.
- **Key interactions**: scroll / CTA clicks / form submit; visit recorded via `RecordVisitAction`.
- **States**: published (renders) · draft/unknown slug (404) · loading (SSR/hydration) · error (render fallback).
- **Gating**: public, unauthenticated; company resolved by slug; throttled ([[../security]]).

## Data

- Owns / writes: `mkt_landing_pages` (status/published_at) (own module).
- Reads: page blocks, embedded form definition (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Reads: embedded form from [[../../forms/_module|marketing.forms]].
- Feeds: page ref on a form submit → conversion counted by [[page-analytics]].
- Shared entity: none written.

## Unknowns

- Custom-domain rendering deferred. See [[../unknowns]].

## Related

- [[../_module|Landing Pages]] · [[page-analytics]] · [[../architecture]]
