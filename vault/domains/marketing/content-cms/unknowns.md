---
domain: marketing
module: content-cms
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Content CMS — Unknowns

Parent: [[_module]]

## Assumed Items

- Status is a simple enum, no spatie states *(assumed)*.
- Author profile (name + bio) comes from the user record *(assumed)* — dedicated author bio field unconfirmed.
- Public blog path `/blog/{company-slug}` *(assumed company-scoped)*.

## Open Questions

- Custom domains for the blog (shared question with landing pages).
- RSS/Atom feed + sitemap generation for the public blog — expected for SEO but unspecced.
- AI draft/outline generation (see [[../_opportunities]]) — additive later.
- Multi-author bylines / guest authors.

## Related

- [[_module]] · [[decisions]] · [[../_opportunities]]
