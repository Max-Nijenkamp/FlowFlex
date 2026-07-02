---
domain: marketing
module: landing-pages
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Landing Pages — Decisions

Parent: [[_module]]

## ADR: Typed block registry (not free HTML)

- **Decision:** Pages are arrays of typed blocks with per-type config schemas; no arbitrary HTML.
- **Consequences:** Responsive-by-construction, validatable, XSS-bounded; new block types are additive.

## ADR: FlowFlex-hosted paths v1; custom domains deferred

- **Decision:** Pages live at `/p/{company-slug}/{page-slug}` *(assumed)*. Custom domains (CNAME) are a later ADR.
- **Consequences:** No DNS/cert plumbing for v1; migration path preserved via the slug scheme.

## ADR: Conversion attributed read-only from Forms

- **Decision:** A form submit carrying the page ref counts as a page conversion; the submission remains owned by Forms.
- **Consequences:** No write into forms tables; landing pages aggregates read-only ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[architecture]] · [[unknowns]]
