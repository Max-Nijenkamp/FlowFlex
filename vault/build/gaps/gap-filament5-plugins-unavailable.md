---
type: gap
severity: medium
category: architecture
status: open
domain: foundation
color: "#F97316"
discovered: 2026-06-11
discovered-in: foundation.scaffold
---

# Four packaged Filament plugins have no Filament 5 release yet

## Context
`foundation.scaffold` installs against Filament v5.6.7 (released recently). Four plugins from the install manifest have no Filament-5-compatible version as of 2026-06-11.

## Problem
Could not install (max support = Filament 3/4, or conflicting spatie/permission constraint):
- `bezhansalleh/filament-shield` — max Filament 4; also requires spatie/laravel-permission ^6/^7 vs our ^8.
- `awcodes/filament-tiptap-editor`
- `saade/filament-fullcalendar`
- `rmsramos/activitylog`

Installed fine: `pxlrbt/filament-excel`, `leandrocfe/filament-apex-charts`, `codewithdennis/filament-select-tree`, `filament/spatie-laravel-media-library-plugin`.

## Impact
Blocks specific later modules, not the scaffold:
- filament-shield → `core.rbac` permission-management UI (workaround: drive spatie/laravel-permission directly + a custom Filament page).
- filament-fullcalendar → calendar UIs (`hr.leave`, `hr.shifts`, `events.events`) — ui-strategy row #4.
- filament-tiptap-editor → rich-text fields (comms, dms) — Phase 2.
- rmsramos/activitylog → `core.audit` log viewer UI.

## Proposed Solution
1. Re-check each package for a Filament 5 release before building its dependent module (ecosystem catching up).
2. If still absent at build time: for filament-shield, build RBAC management as a custom Filament page over spatie/laravel-permission (already a hard dep); for calendar, evaluate a direct FullCalendar JS embed in a custom page (ui-strategy row #4 already allows a custom page); for tiptap, use the awcodes editor's v5 branch or a Livewire wrapper.
3. Log an ADR if a package is permanently swapped.

## Related
- [[domains/foundation/laravel-scaffold]]
- [[architecture/ui-strategy]]
- [[architecture/packages]]
