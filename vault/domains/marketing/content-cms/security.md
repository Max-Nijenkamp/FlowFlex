---
domain: marketing
module: content-cms
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Content CMS — Security

Parent: [[_module]]

Public read surface with authored HTML + a Meilisearch-backed search endpoint.

## Public visibility

- `/blog/{company-slug}` + post pages render **published-only**, company-scoped; draft/scheduled → 404 publicly.
- Search returns published-only for the right company (index scoped at write time).

## Content purification

Body is purified (ezyang/htmlpurifier) on save; the public renderer emits no raw user HTML. SEO meta is escaped.

## Rate limiting

| Action | Category | Limiter |
|---|---|---|
| Public blog Index/Show + search endpoint | public read (Meilisearch-abuse guard) | `api` *(assumed — no dedicated public-endpoint limiter exists yet; per-IP throttle to protect Meilisearch is an open reconciliation item, see [[unknowns]])* ([[../../../architecture/security]], [[../../../architecture/search]]) |

Publish reindexes the post into Meilisearch via a queued Scout job (internal projection, not a user-abusable external call), so the publish action itself carries no named panel limiter.

## Permissions

| Permission | Grants |
|---|---|
| `marketing.cms.view-any` | Post + category list |
| `marketing.cms.create` | Create a post / category |
| `marketing.cms.update` | Edit a post / category |
| `marketing.cms.delete` | Soft-delete a post / category |
| `marketing.cms.publish` | Publish / schedule / unpublish a post (distinct from edit) |

Publish is distinct from edit so an author can draft without publish rights. The `PublishScheduledPostsCommand` transition is system-driven, not a user permission. Seeded in `PermissionSeeder`. Resources gate on `canAccess()`.

**Verb-per-command check:** the publish / schedule / unpublish command actions map to `marketing.cms.publish`; standard CRUD verbs are covered above.

## Data ownership

Writes only `mkt_posts`, `mkt_post_categories` (+ its Meilisearch projection). Author + media read-only from their owners ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[architecture]] · [[../../../architecture/search]] · [[../../../architecture/security]]
