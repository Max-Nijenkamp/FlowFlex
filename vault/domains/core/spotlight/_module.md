---
domain: core
module: spotlight
type: module
build-status: in-progress
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Spotlight

Panel-scoped ⌘K / Ctrl+K quick-search palette. A Livewire overlay, rendered on every authenticated panel page, that jumps to navigation, offers quick-create actions, and runs the panel's global search — all `canAccess()`-filtered. Built platform capability (no flat spec existed; reconstructed from code).

## Module-key

`core.spotlight` *(assumed)*

**Priority:** v1-core *(assumed)*  
**Panel:** all panels (chrome overlay via render hooks)  
**Permission prefix:** `core.spotlight` (no permissions — mirrors each panel's own `canAccess()` boundary)  
**Tables:** none of its own — stateless read-only navigation/search overlay  
**Events:** fires none · consumes none

## Core Features

- ⌘K / Ctrl+K opens an Alpine overlay teleported to `<body>`; ESC closes, up/down navigate, Enter activates.
- Three result sources, all `canAccess()`-filtered: panel **Resources** (nav + quick-create), panel **Pages** (nav), and the panel **global search provider** (query ≥2 chars).
- Restores panel context inside the computed (Livewire updates don't run panel routing) — a known null-panel pitfall.
- Two render hooks: the palette itself on `BODY_END` (authenticated only) and a topbar trigger button on `GLOBAL_SEARCH_BEFORE`.

## Sibling notes

- [[architecture]] — Livewire component, 3 result sources, panel-context restore, render hooks + flow diagram
- [[security]] — canAccess filtering, authenticated-only render, per-panel scoping
- [[unknowns]] — module-key / priority assumptions
- [[features/keyboard-palette]] — ⌘K UX + Alpine overlay

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | foundation.panels | reads each panel's Resources / Pages / global search provider |

Sibling in panel chrome: [[../notifications/_module]] (bell). Both are chrome-level render-hook injections.

## Build Manifest (flat paths)

```
app/Livewire/Spotlight.php
resources/views/livewire/spotlight.blade.php
app/Providers/AppServiceProvider.php   (registers BODY_END palette + GLOBAL_SEARCH_BEFORE trigger)
tests/Feature/SpotlightTest.php
```

## Test Checklist

- [x] Tenant isolation: a user in company A's panel sees only company A records (results drawn from that one panel's `CompanyScope`-filtered sources)
- [x] Module gating: n/a (platform chrome, always active — mirrors each panel's `canAccess()`)
- [x] Results are `canAccess()`-filtered: a resource/page the user cannot access never appears
- [x] Renders on an authenticated panel page; NOT on login (no panel user)
- [x] Panel context restored mid-Livewire-update (null-panel pitfall) — results resolve for the right panel
- [x] Quick-create only offered when `hasPage('create') && canCreate()`

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | none | — | Spotlight fires no domain events |
| consumes | none | — | Spotlight consumes no domain events; it read-queries every panel's own Resources/Pages/search provider live |

Data ownership: spotlight owns no tables of its own — it is a stateless navigation/search overlay that reads the current panel's Filament Resources, Pages, and global-search provider read-only (all already `canAccess()`- and `CompanyScope`-filtered), and effects other domains only via events (there are none) ([[../../../security/data-ownership]]).

## Related

- [[../notifications/_module]]
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../architecture/filament-patterns]] · [[../../../architecture/patterns/filament-panel-chrome]]
- [[../../../security/authn-authz]] · [[../../../security/data-ownership]] · [[../../../glossary]]
