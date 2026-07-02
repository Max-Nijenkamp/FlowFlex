---
type: meta
category: template
status: stable
color: "#6B7280"
updated: 2026-06-20
---

# Feature Note Template

The skeleton for every `feature` note (a vertical slice inside a module). Companion to the frozen module
[[spec-template]]. Introduced by [[../decisions/decision-2026-06-20-full-mapping-conventions]].

```markdown
---
domain: <domain-key>
module: <module-slug>
feature: <feature-slug>
type: feature
build-status: planned
status: unverified | wip
color: "#4ADE80"
updated: YYYY-MM-DD
---

# <Feature Name>

One or two lines: what this slice does and why it exists.

## Behaviour

Bullet the rules / flow / states of the feature (the "what happens"). Include the state machine
transitions if it has one.

## UI

<!-- the full block from architecture/patterns/feature-ui-spec -->
- **Kind**: simple-resource | custom-page | widget | public-vue | background
- **Page**: name + route
- **Layout**: …
- **Key interactions**: …
- **States**: empty · loading · error · selected
- **Gating**: which permission(s)

## Data

- Owns / writes: <this module's tables only>
- Reads: <other domains' read APIs>
- Cross-domain writes: via events only (never another domain's tables — [[../../../security/data-ownership]])

## Relations

- Consumes: `<Event>` from `<domain.module>` → <effect>
- Feeds: `<Event>` → consumed by `<domain.module>`
- Shared entity: <reference data owned elsewhere>

## Unknowns

- `*(assumed)*` items, open questions, UNVERIFIED.

## Related

- [[../_module|<Module>]] · sibling features · cross-domain links
```
