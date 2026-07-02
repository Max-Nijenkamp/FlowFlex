---
domain: support
module: knowledge-base
feature: article-authoring
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Article Authoring

Agents write, categorise, version, and publish help articles.

## Behaviour

- Article: title, slug (auto), rich-text body (Tiptap, purified), category, status (draft/published), author.
- Publish/unpublish flips status + stamps `published_at`.
- On edit, the prior body is appended to `revisions` (jsonb, capped 20 *(assumed)*).
- Categories + sub-categories organise articles.

## UI

- **Kind**: simple-resource — `KbArticleResource` (+ `KbCategoryResource`) CRUD with a Tiptap editor.
- **Page**: `KbArticleResource` (`/support/kb/articles`), `KbCategoryResource` (`/support/kb/categories`).
- **Layout**: article list (title, category, status badge, view/helpful counts); form = title + category + Tiptap body + status; publish action.
- **Key interactions**: edit body (purified on save); publish/unpublish action; reorder categories (tree order).
- **States**: empty (no articles → "write your first article" CTA) · loading (save) · error (empty body / XSS stripped) · selected (editing an article).
- **Gating**: view `support.kb.view-any`; create/update `support.kb.create`/`support.kb.update`; publish `support.kb.publish`; categories `support.kb.manage-categories`.

## Data

- Owns / writes: `sup_kb_articles`, `sup_kb_categories`.
- Reads: none cross-domain.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `KbService::suggestFor` reads these articles for [[../../tickets/_module|support.tickets]] reply suggestions (soft).
- Shared entity: none.

## Unknowns

- Revision cap *(assumed 20)*; AI stale-article flagging deferred — [[../unknowns]].

## Related

- [[../_module|Knowledge Base]] · [[./public-help-centre]]
