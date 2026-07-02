---
domain: marketing
module: content-cms
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Content CMS — Security

Parent: [[_module]]

Public read surface with authored HTML + a Meilisearch-backed search endpoint.

## Public visibility

- `/blog/{company-slug}` + post pages render **published-only**, company-scoped; draft/scheduled → 404 publicly.
- Search returns published-only for the right company (index scoped at write time).

## Content purification

Body is purified (ezyang/htmlpurifier) on save; the public renderer emits no raw user HTML. SEO meta is escaped.

## Rate limiting (medium)

Public blog + search routes carry a throttle to protect Meilisearch from query-abuse ([[../../../architecture/security]], [[../../../architecture/search]]).

## Permissions

`marketing.cms.view-any` · `marketing.cms.create` · `marketing.cms.update` · `marketing.cms.publish`. Publish is distinct from edit. Resources gate on `canAccess()`.

## Data ownership

Writes only `mkt_posts`, `mkt_post_categories` (+ its Meilisearch projection). Author + media read-only from their owners ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[architecture]] · [[../../../architecture/search]] · [[../../../architecture/security]]
