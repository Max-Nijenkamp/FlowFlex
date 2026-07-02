---
domain: marketing
module: landing-pages
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Landing Pages — Unknowns

Parent: [[_module]]

## Assumed Items

- Public path `/p/{company-slug}/{page-slug}` *(assumed)*.
- Custom-domain support deferred to a later ADR.
- Templates are seeded layouts *(assumed)*.

## Open Questions

> [!warning] UNVERIFIED
> How a page conversion is counted without a cross-domain write: likely landing-pages consumes a `FormSubmissionReceived` carrying a `landing_page_id` in `fields`/meta and increments its own counter. The page-ref propagation from an embedded form is unspecified.

- A/B testing of whole pages (vs. campaign subject A/B) — out of scope v1?
- Block set finality — which blocks ship in v1's registry.
- AI page generation (see [[../_opportunities]]) — additive later.

## Related

- [[_module]] · [[decisions]] · [[../_opportunities]]
