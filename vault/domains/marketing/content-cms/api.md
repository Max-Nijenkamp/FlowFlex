---
domain: marketing
module: content-cms
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Content CMS — API (DTOs)

Parent: [[_module]] · See also [[architecture]]

No cross-domain service contract; **fires/consumes no events**.

## DTOs

### CreatePostData (input → `PostResource`)

| Field | Type | Validation |
|---|---|---|
| title | string | required, max:255 |
| body | text | required, purified |
| excerpt | text? | |
| category_id | ulid? | exists, same company |
| tags | string[] | |
| published_at | datetime? | future → `scheduled`; null/past → immediate on publish |
| meta_title / meta_description | string? | SEO |
| og_image | string? | media path |

## Reads (cross-domain)

- Author display (user name + bio) from core users.
- Featured image / OG image from [[../../core/file-storage/_module|core.files]].

## Events

None ([[../../../architecture/event-bus]]).

## Related

- [[_module]] · [[architecture]] · [[security]]
