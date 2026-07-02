---
domain: marketing
module: landing-pages
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Landing Pages — API (DTOs)

Parent: [[_module]] · See also [[architecture]]

No cross-domain service contract; **fires/consumes no events**.

## DTOs

### CreateLandingPageData (input → `LandingPageResource`)

| Field | Type | Validation |
|---|---|---|
| name | string | required |
| blocks | array | each `{type, config}`; type in `BlockRegistry`; config schema-valid per type; form block → referenced form exists + active |
| meta_title / meta_description | string? | |
| og_image | string? | media path |

## Reads (cross-domain)

- Form definitions from [[../forms/_module|marketing.forms]] (form block).
- Media URLs from [[../../core/file-storage/_module|core.files]].

## Events

None. Visit + conversion are internal counters ([[../../../architecture/event-bus]]).

## Related

- [[_module]] · [[architecture]] · [[security]]
