---
type: architecture
category: pattern
pattern-key: custom-page-checklist
status: stable
last-reviewed: 2026-07-02
color: "#A78BFA"
---

# Custom Filament Page Checklist

The resource checklist ([[architecture/patterns/filament-resource-checklist]]) gates CRUD resources; this is its twin for **custom pages** — the interactive kinds in [[architecture/ui-strategy]] rows 3–11 and 17–19 (Kanban, Calendar, Gantt, Dashboard, Wizard, Inbox, Report builder, Org/Tree, Gallery, Heat-map, Spatial). A custom page is a hand-built Livewire component: none of the resource-level safety nets apply automatically, so every item below must be stated in code. Every custom page passes this before its module is `complete`.

---

## Per-page checklist

1. **canAccess() is explicit** — permission `can(...)` **and** `BillingService::hasModule(...)`. Filament does **not** auto-gate custom pages the way it gates resources; an unstated `canAccess()` leaves the page open to every authenticated user ([[architecture/filament-patterns]] #1). Never inherited, never weakened.
2. **Page plumbing is correct** — `$view` is an **instance** property, not static (static fails silently), and `getSlug(?Panel $panel = null): string` has the right signature. Both are the top pitfalls in [[architecture/patterns/custom-pages]].
3. **Blueprint conformance** — the page contains every mandatory region of its kind in [[architecture/patterns/page-blueprints]], and cites that kind in the module's `## Filament Artifacts` table. A page matching no blueprint is not buildable — it needs an ADR + new ui-strategy row first.
4. **All four UX states designed** — first-use empty, emptied, filtered-out (names *why* it's empty + a clear/widen action), and error (human copy + Retry, not a manual refresh) per [[architecture/patterns/ux-states]]. "No records found" is never acceptable. Each region designs its own states per the blueprint.
5. **Skeleton loading, no spinner walls** — data sections defer (`wire:init` / `$isLazy` widgets) and render a skeleton matching the final layout, per [[architecture/patterns/perceived-performance]]. Spinners are a bug on any data view.
6. **Realtime choice cited** — the page states none / poll (≥30s) / Reverb, and it matches its blueprint's default. Never poll under 15s — use Reverb instead ([[architecture/ui-strategy]] realtime rule). Any change from the blueprint default is justified in `security.md`.
7. **DTOs cross the JS boundary, never Eloquent** — any data passed to Blade/Alpine/JS is a `spatie/laravel-data` DTO or plain array, never a live Eloquent model or collection ([[architecture/patterns/dto-pattern]]).
8. **Actions are rate-limited by category** — any action that sends comms, mutates money/inventory, generates files/PDFs, or calls an external API names a Redis limiter (default `RateLimiter::for('panel-action')`, 30/min/user) per [[decisions/decision-2026-07-02-rate-limit-and-token-hardening]]. Cited in the module's `security.md`.
9. **Stale-record guard on edit surfaces** — any surface that edits a shared record carries the `updated_at` stale-check and surfaces the conflict notification, per [[architecture/patterns/optimistic-locking]] (money/inventory/capacity mutations use the pessimistic tier instead). The page's write path matches the module's `## Concurrency` declaration.
10. **Tests exist** — a Livewire test asserts the page renders, `canAccess()` gates correctly (permitted vs denied, and module-off), and the primary interaction works; a Playwright smoke asserts one kind-specific interaction (Kanban: drag a card; Calendar: switch view; Wizard: advance a step; Inbox: select a conversation; Dashboard: widgets render) per [[decisions/decision-2026-07-02-browser-test-convention]]. Expectations come from the blueprint.
11. **Mobile / collapsed-sidebar behavior stated** — the blueprint's mobile fallback is implemented and noted (Kanban column pager, Inbox master→detail, Gantt read-only scroll, etc.); the page renders usably with the sidebar collapsed.

## Verification

`/flowflex:verify` (live curl sweep) after any panel-touching session, plus the Playwright smoke from item 10 as the pre-merge gate. The curl sweep catches HTTP/tenant regressions ([[architecture/patterns/tenant-context-pitfalls]]); the browser smoke catches JS/asset breakage that Livewire tests miss.

## Related

- [[architecture/patterns/page-blueprints]] — the per-kind regions this checklist verifies (item 3)
- [[architecture/patterns/filament-resource-checklist]] — the twin gate for CRUD resources
- [[architecture/patterns/custom-pages]] — PHP/Blade structure and pitfalls (items 1–2)
- [[architecture/ui-strategy]] — which kinds are custom pages; realtime rule
- [[architecture/way-of-working]] — DoD references this file for custom-page modules
