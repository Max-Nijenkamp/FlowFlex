---
domain: support
module: knowledge-base
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Knowledge Base — Local Decisions

## Decided

- **Revisions in jsonb, not a versions table.** Prior bodies live in a capped (20) `revisions` jsonb column rather than a separate `sup_kb_article_versions` table — KB volume is low and full audit isn't required. Overridable if legal retention demands it.
- **Public help centre is Vue + Inertia, not a Filament page.** External/unauthenticated surface → ui-strategy row #16, guest guard.
- **Feedback is anonymous + rate-limited.** No visitor identity stored; counts only, guarded by a named limiter.

## Assumed (overridable via ADR)

- Help-centre URL scheme `/help/{company-slug}` *(assumed)*.
- Revision cap 20 *(assumed)*.

## Related

- [[./unknowns]] · [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
