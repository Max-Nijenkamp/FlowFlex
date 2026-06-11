---
type: adr
date: 2026-06-11
status: decided
domain: All
color: "#F97316"
---

# Perceived-Performance Standard (Skeletons, Optimistic UI, Ease-Out Motion)

## Context
Founder directive (2026-06-11): loading icons feel slow; skeletons preview layout so waits feel shorter; button taps should assume success and update locally (optimistic UI); animations should start fast and end slow to hide latency. Must apply to existing surfaces and every future module.

## Decision
New pattern file [[../../architecture/patterns/perceived-performance]] is mandatory: (1) skeleton loaders everywhere, never spinners — shared Blade components + lazy Filament widgets with skeleton placeholders; (2) optimistic UI for low-risk quick actions (toggles, status moves, kanban drag, mark-as-read) with rollback on error — never for payments/destructive/four-eyes ops; (3) all transitions ease-out 150–200ms entrances / 100ms exits, staggered list entrances. Added as Definition-of-Done item 10 in [[../../architecture/way-of-working]].

## Consequences
- Retrofit pass over existing Filament panels (widgets lazy + placeholders, deferred tables, kanban already optimistic)
- New shared component library `resources/views/components/skeleton/`
- Spec `patterns:` lists gain `perceived-performance` where UI-heavy

## Related
- [[../../architecture/ui-strategy]] — banner note added
