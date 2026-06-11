---
type: pattern
concern: perceived-performance
color: "#A78BFA"
---

# Perceived Performance

Speed users *feel* matters more than speed servers measure. Three mandatory techniques for every UI surface (Filament panels AND Vue public site). New modules apply these from day one; this file is loaded via `patterns: [perceived-performance]`.

---

## 1. Skeleton loading screens (never spinners)

A spinner says "wait". A skeleton says "here is your layout, content is coming" — the user pre-parses the page structure, so the wait feels shorter.

**Rules:**
- No loading icons/spinners for any view that loads data. Show a skeleton that mirrors the real layout (table rows, card grids, stat tiles, form fields).
- Skeleton shapes match the final content's dimensions — no layout shift when data lands.
- Subtle pulse animation (`animate-pulse`), neutral gray (`bg-gray-200 dark:bg-gray-700`), rounded to match the component.

**Filament:**
- Widgets: lazy-load (`protected static bool $isLazy = true`) — Filament renders a placeholder; override `placeholder()` to return the skeleton view, not the default spinner.
- Tables: `->deferLoading()` + custom `->emptyState()`-style skeleton via the shared Blade component while deferred.
- Custom pages: wrap data sections in `wire:init` deferred loading with `@if(!$readyToLoad) <x-skeleton.../> @endif`.

**Shared Blade components** (in `resources/views/components/skeleton/`):
- `<x-skeleton.table :rows="8" :cols="5" />`
- `<x-skeleton.stat-cards :count="4" />`
- `<x-skeleton.form :fields="6" />`
- `<x-skeleton.list :rows="6" />`

**Vue (public site):** `<SkeletonTable>`, `<SkeletonCard>` components; show during Inertia partial reloads via `router.on('start')` state.

---

## 2. Optimistic UI

A button tap assumes success: update local state **instantly**, send the request in the background, reconcile on response. Never make the user watch a round-trip for an action that succeeds 99% of the time.

**Apply to:** toggles, status changes, approve/reject, kanban drag-drop, favoriting/pinning, list reorder, notification mark-as-read, simple creates (add comment/note).

**Do NOT apply to:** payments, destructive deletes, anything four-eyes (payroll approve stays explicit), anything where failure is common (external API calls).

**Rules:**
- Flip the UI state immediately on click (Livewire: update the property before the action's server call resolves; Alpine: `x-on:click` local mutation + `$wire` call).
- On server error: roll back the local state AND show a danger notification explaining the action did not stick.
- Optimistic rows get no special styling — they look committed. (A "pending" look defeats the purpose.)
- Idempotent server endpoints — a retry after rollback must be safe.

**Kanban drag-drop is the reference implementation:** card moves visually on drop, `moveDeal` runs after; on exception the board re-renders from server state.

---

## 3. Animation psychology — start fast, end slow

Latency hidden inside motion is latency the user does not perceive.

**Rules:**
- All transitions use **ease-out** (fast start, slow settle): `transition ease-out duration-200`. Never linear, never ease-in for entrances.
- Panel/modal/slide-over entrances: 150–200ms ease-out. Exits: 100ms (leaving should feel instant).
- When an action triggers navigation or a heavy reload, start the transition animation immediately on click — the request runs behind the animation.
- Stagger list/card entrances by 20–30ms per item (first items appear immediately = page feels loaded).
- Progress that must be shown (imports, exports): progress bars start fast then decelerate — perceived as "nearly done" rather than "stuck at 0".

---

## Definition of done hook

Every module's UI review checks: no spinner anywhere, skeletons match layout, listed optimistic actions are instant, transitions ease-out. See [[../way-of-working]] quality gates.

## Related

- [[custom-pages]] — custom Filament pages host most of these surfaces
- [[../ui-strategy]] — which UI tech per screen
- [[../websockets]] — realtime updates complement optimistic state
