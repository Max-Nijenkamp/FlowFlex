---
domain: support
module: knowledge-base
feature: public-help-centre
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Public Help Centre

Customer-facing, unauthenticated portal for browsing and searching published articles.

## Behaviour

- Published articles browsable at `/help/{company}` and `/help/{company}/{category}/{slug}` *(assumed slug scheme)*.
- Meilisearch search limited to published + company.
- Each article shows "Was this helpful?" (thumbs up/down) → `RecordFeedbackAction`, rate-limited; view logged via `RecordArticleViewAction`.
- Drafts and other companies' content never appear.

## UI

- **Kind**: public-vue — external unauthenticated surface, Vue + Inertia, ui-strategy row #16.
- **Page**: Help Centre (`/help/{company}` index, `/help/{company}/{category}/{slug}` article) — `HelpCentreController` + `resources/js/Pages/Help/{Index,Category,Article}.vue`.
- **Layout**: index = search bar + category tiles + popular articles; article page = breadcrumb, body, helpful widget, related articles.
- **Key interactions**: search-as-you-type (published-only); thumbs up/down (optimistic, rate-limited); category drill-down.
- **States**: empty (no published articles → friendly placeholder) · loading (search skeleton) · error (search fails → retry) · selected (article open, feedback submitted state).
- **Gating**: none (guest guard); feedback + view endpoints rate-limited per visitor.

## Data

- Owns / writes: `sup_kb_articles` counters (`view_count`, `helpful_count`, `not_helpful_count`) — its own tables.
- Reads: published articles/categories (own tables, filtered).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing (public reads).
- Feeds: deflects tickets away from [[../../tickets/_module|support.tickets]] (indirect).
- Shared entity: none.

## Unknowns

- Custom-domain / white-label help centre deferred; slug source unconfirmed — [[../unknowns]].

## Related

- [[../_module|Knowledge Base]] · [[./article-authoring]] · [[../../../../architecture/search]]
