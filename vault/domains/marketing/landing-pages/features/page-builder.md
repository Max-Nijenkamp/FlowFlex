---
domain: marketing
module: landing-pages
feature: page-builder
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Page Builder

Assemble a landing page from typed content blocks and set its SEO.

## Behaviour

- Add blocks from the registry (hero, features, testimonial, CTA, form, footer); reorder; edit rich content (purified) per block.
- Form block references an existing active form.
- Set slug + SEO (meta title/description, OG image). Draft by default.

## UI

- **Kind**: custom-page
- **Page**: block-builder inside `LandingPageResource` (`/marketing/landing-pages/{id}/edit`) — Landing Pages nav group. A block **repeater** with live preview goes beyond plain table+form, so custom-page.
- **Layout**: left = ordered block list (add/reorder/delete); centre = live preview; right rail = SEO + publish controls.
- **Key interactions**: drag to reorder blocks; edit block config in a panel; toggle preview device; publish/unpublish.
- **States**: empty (no blocks → "add your first block") · loading (preview render) · error (invalid block config; form block references missing/inactive form) · selected (block highlighted, its config panel open).
- **Gating**: `marketing.landing-pages.create` / `.update`; publish needs `.publish`.

## Data

- Owns / writes: `mkt_landing_pages` (own module).
- Reads: form list (form block), media (images) — read-only.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Reads: forms from [[../../forms/_module|marketing.forms]]; media from [[../../../core/file-storage/_module|core.files]].
- Feeds: page config consumed by [[publish-render]].
- Shared entity: none written.

## Unknowns

- Final v1 block set. See [[../unknowns]].

## Related

- [[../_module|Landing Pages]] · [[publish-render]] · [[../architecture]]
