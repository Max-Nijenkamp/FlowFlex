---
domain: marketing
module: content-cms
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Content CMS — Decisions

Parent: [[_module]]

## ADR: Simple status enum, not spatie states

- **Decision:** `draft → scheduled → published` is a plain enum + a scheduler command *(assumed)*, since transitions are linear with no side-effect fan-out.
- **Consequences:** Lighter than a state-machine module; revisit if reversible/branching workflow is needed.

## ADR: Published-only public + search scope

- **Decision:** Public blog + Meilisearch queries only ever surface published, company-scoped posts; draft/scheduled are invisible publicly.
- **Consequences:** No content leak; search index written published-only.

## ADR: Company-scoped blog path

- **Decision:** Blog served at `/blog/{company-slug}` *(assumed)* — each company's blog is its own namespace.
- **Consequences:** Multi-tenant-safe public content; custom domains a later concern (shared with landing pages).

## Related

- [[_module]] · [[architecture]] · [[unknowns]]
