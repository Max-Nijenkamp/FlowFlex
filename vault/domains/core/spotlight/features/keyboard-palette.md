---
domain: core
module: spotlight
feature: keyboard-palette
type: feature
build-status: in-progress
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Spotlight — Keyboard Palette

Parent: [[../_module]]

The ⌘K / Ctrl+K UX layer — an Alpine overlay over the Livewire component ([[../architecture]]).

## Opening

- Global key: `keydown.window.meta.k` (macOS ⌘K) / `.ctrl.k` (Windows/Linux).
- Custom event: `ff-spotlight-open`, dispatched by the topbar "Search this panel…" button (320px, ⌘K `kbd` hint) registered on `PanelsRenderHook::GLOBAL_SEARCH_BEFORE`.

## In-palette keyboard model

- The overlay (`x-data`) is teleported to `<body>` so it sits above all panel chrome.
- **ESC** closes.
- **Up / Down** move the active highlight through `.ff-spotlight-result` items.
- **Enter** clicks the active result (navigate / quick-create).

## Markup

CSS classes `ff-spotlight-overlay` (backdrop) / `ff-spotlight` (panel). View: `resources/views/livewire/spotlight.blade.php`.

## UI

- **Kind**: custom-page — a custom Livewire component (`App\Livewire\Spotlight`) with an Alpine overlay; treated as the ⌘K palette (per constitution, spotlight/keyboard-palette is a custom Livewire component ≈ custom-page). Not a routed page — it's a chrome overlay injected via render hooks.
- **Page**: no dedicated route — rendered on **every authenticated panel page** via `PanelsRenderHook::BODY_END`, plus a topbar trigger button via `PanelsRenderHook::GLOBAL_SEARCH_BEFORE`. Component: `spotlight` (`resources/views/livewire/spotlight.blade.php`).
- **Layout**: full-screen `ff-spotlight-overlay` backdrop teleported to `<body>`, centering an `ff-spotlight` command panel — a search input at top, grouped results below ("Navigation", "Quick actions", and one group per global-search category).
- **Key interactions**:
  1. Open via `⌘K` (`keydown.window.meta.k`) / `Ctrl+K` (`.ctrl.k`) or the topbar "Search this panel…" button (dispatches `ff-spotlight-open`).
  2. Type ≥2 chars → Livewire recomputes grouped results (panel context restored via `Filament::setCurrentPanel`).
  3. `Up`/`Down` move the active highlight through `.ff-spotlight-result`; `Enter` activates it (navigate or quick-create); `ESC` closes.
- **States**: empty (no query → nav + quick-create groups only) · loading (Livewire recompute between keystrokes) · error (n/a — degrades to empty results) · selected (active `.ff-spotlight-result` highlight moved by arrow keys).
- **Gating**: authenticated-only (`BODY_END` renders only when `Filament::auth()->check()`); every result is `canAccess()`-filtered (resource nav/quick-create need `canAccess()` + `canCreate()`; page nav needs page access; global search honours per-record authorization). No dedicated permission string — it mirrors each panel's own `canAccess()` boundary.

## Data

- Owns / writes: no tables of its own — Spotlight is a stateless read/navigation overlay. It persists nothing.
- Reads: read-only, from the **current panel only** — that panel's Filament Resources (nav + quick-create), Pages (nav), and its global-search provider (`$panel->getGlobalSearchProvider()->getResults()`). All underlying records are already company-scoped via `CompanyScope`; Spotlight adds no cross-panel/cross-tenant aggregation.
- Cross-domain writes: none — effects other domains only via events (there are none) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events).
- Feeds: none.
- Shared entity: reads every panel's own Resources/Pages/search provider read-only — those entities are owned by their respective domain modules; Spotlight never mutates them, only links to them.

## Test Checklist

### Unit
- [x] Result grouping caps: nav 8 / quick-create 5 / global-search 6 per category
- [x] Quick-create entry produced only when `hasPage('create') && canCreate()`

### Feature (Pest)
- [x] `getResults()` restores panel context (`setCurrentPanel`) and returns only `canAccess()`-permitted entries
- [x] Results scoped to the bound `panelId` — no cross-panel/cross-tenant aggregation
- [x] Global-search group appears only for query ≥2 chars

### Livewire
- [x] Component renders on an authenticated panel page and NOT on login (no panel user)
- [x] Arrow keys move the active highlight; Enter navigates to the selected result; ESC closes

## Related

- [[../_module]] · [[../architecture]] · [[../security]]
- [[../../../../security/data-ownership]] · [[../../../../architecture/patterns/filament-panel-chrome]]
