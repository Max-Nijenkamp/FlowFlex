---
type: architecture
category: pattern
pattern-key: panel-chrome
status: stable
last-reviewed: 2026-06-14
color: "#A78BFA"
---

# Filament Panel Chrome — render hooks + full-height sidebar

How the Switchboard+ panel shell is built without forking Filament's Blade. Everything here is **render hooks + skin CSS** (`resources/css/filament/flowflex-skin.css`), so it survives Filament upgrades. Visual targets: [[../../frontend/design-system]] §12. Skin selector rules: [[../filament-patterns]] items 12, 16.

---

## 1. Render hooks own the chrome

Registered once in `AppServiceProvider::boot()` (apply to every panel, guard with `Filament::auth()->check()` where a user is required):

| Hook | What we inject | Support class |
|---|---|---|
| `SIDEBAR_LOGO_BEFORE` | light wordmark at sidebar head (`.ff-side-brand`) | inline |
| `SIDEBAR_FOOTER` | collapse toggle + "Your panels" switcher chips + user card (account menu) | `filament.chrome.sidebar-footer` view |
| `GLOBAL_SEARCH_BEFORE` | 320px search trigger w/ ⌘K → dispatches `ff-spotlight-open` | inline |
| `BODY_END` | `@livewire('spotlight')` palette | `App\Livewire\Spotlight` |
| `SIMPLE_LAYOUT_START` / `_END` | login brand mark + footer strip | inline |

**Provider HTML is NOT scanned by the theme build** (filament-patterns item 12) — every class in a hook string must be plain CSS defined in `flowflex-skin.css`, never a Tailwind utility (it won't exist in the compiled theme). This is why all chrome uses `ff-*` classes.

## 2. Full-height sidebar (the layout fix)

Filament renders the topbar ABOVE the sidebar+main row. The design wants the ink sidebar to own the full left edge. Don't fork the layout — pin the sidebar and offset the rest (skin, desktop only):

```css
@media (min-width: 1024px) {
    .fi-sidebar { position: fixed; inset-block: 0; height: 100dvh; width: 248px; z-index: 35; }
    .fi-topbar-ctn, .fi-main-ctn { margin-inline-start: 248px; }
    /* collapsed icon rail — keep both aligned via :has() */
    .fi-body:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-topbar-ctn,
    .fi-body:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-main-ctn { margin-inline-start: 4.5rem; }
    .fi-body:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-sidebar { width: 4.5rem; }
}
```

Brand lives in the sidebar header, so **force the header visible** (Filament hides it when a topbar carries the logo) and **hide the duplicates**:

```css
.fi-sidebar-header-ctn, .fi-sidebar-header { display: flex !important; }
.fi-sidebar-header-logo-ctn,                 /* native sidebar logo (a div, not <a>) */
.fi-topbar .fi-logo, .fi-topbar-logo-ctn,    /* native topbar logo */
.fi-global-search-ctn, .fi-global-search {   /* native search — Spotlight replaces it */
    display: none !important;
}
```

Gotcha: the native sidebar logo is `.fi-sidebar-header-logo-ctn` (a `div`), so `.fi-sidebar-header > a` selectors miss it. Always confirm against rendered markup.

## 3. Panel switching = sidebar chips only · account menu = sidebar user card

Cross-panel navigation lives in the `SIDEBAR_FOOTER` chips (`canAccessPanel`-filtered), NOT in the profile dropdown. `userMenuItems(PanelSwitchItems::…)` was removed — two switchers is one too many.

The topbar user menu (`.fi-user-menu`) is hidden entirely (owner decision 2026-07-04 — it duplicated the sidebar user card). The sidebar user card is an Alpine popover trigger: Profile link (`filament()->getProfileUrl()`) + Sign out (POST `filament()->getLogoutUrl()`), opening upward (`.ff-user-menu-panel`).

Owner decision 2026-07-04 (round 2): **no topbar crumb at all** — `TopbarCrumb`/`TOPBAR_START` removed; native page-header breadcrumbs (`.fi-breadcrumbs`) render on the pages themselves, styled faint 13px. The desktop collapse toggle (`.fi-topbar-collapse-sidebar-btn-ctn`) is hidden from the topbar too; a `.ff-side-toggle` button in the sidebar footer drives `$store.sidebar` instead (mobile hamburger untouched). Topbar carries ONLY: search trigger + bell.

## 4. Spotlight ⌘K (BUILT 2026-07-04)

`App\Livewire\Spotlight` + `resources/views/livewire/spotlight.blade.php` (BODY_END, auth-guarded, both panels) — panel-scoped: nav (resources+pages, `canAccess`-filtered), quick-create (`canCreate` + has create page), records via `$panel->getGlobalSearchProvider()` at ≥2 chars. Restore panel context with `Filament::setCurrentPanel()` (Livewire update requests don't run panel routing — `panelId` is a Livewire prop set at mount). Alpine gotcha: use **server-rendered stable `data-index`** for active-row tracking. Opens via `ff-spotlight-open` window event (trigger button) AND `meta+k`/`ctrl+k` window keydown bound in the component itself; the trigger's `<kbd>` label is per-OS (`⌘K` mac / `Ctrl K` win via `navigator.platform`). Styling `.ff-spotlight-*` plain CSS in the skin. filament-patterns item 14.

## 5. Brand-aware accents

The skin never hardcodes a domain color — it rides Filament's `--primary-*` (set by each panel's `->colors()`). One skin file, every panel correct: active nav item, stat corner ticks, pagination, selection bar all use `var(--primary-500)`.

## 6. Skin gotchas (hard-won 2026-07-04 — measure, don't guess)

Debug method that found all of these: a Playwright `evaluate()` probe dumping `getBoundingClientRect()` + computed styles up the ancestor chain beats staring at screenshots. See `/flowflex:screenshot`.

1. **`scrollbar-gutter: stable` skews narrow layouts.** `.fi-sidebar-nav` reserves ~15px right gutter permanently — in the 72px icon rail that pushed every nav item 7.5px left of the axis while padding/margin all measured 0. Fix: `scrollbar-gutter: auto` in the collapsed state.
2. **Grid gaps cannot be cancelled by item margins.** A zero-height schema component still costs its row-gap(s); negative margins only shrink the track, never the gap. To make a conditionally-hidden element truly zero-footprint inside a Filament schema: don't give it its own schema row — attach it to the field via `->belowContent(...)`, then zero the field wrapper's own `row-gap` (`.fi-fo-field-content-col`, 8px) scoped with `:has()`, re-adding sibling spacing via margins.
3. **`.fi-sc-text` renders as an inline `<span>`** — `text-align: center` needs `display: block` first.
4. **Vendor collapsed-sidebar rules out-specific bare skin selectors** — prefix `.fi-body` when overriding icon-rail styles.
5. **Smooth reveal without JS plugins**: `display: grid; grid-template-rows: 0fr → 1fr` + opacity + margin, transitions on all three, `prefers-reduced-motion` gated (used by the password checklist).
6. **`fillForm()` silently no-ops on auth-page schemas in Livewire tests** — use `->set('data.*', ...)`; see gap-fillform-noop-auth-pages.
7. **Playwright notification carryover**: asserting on `body.innerText` right after an action can match the *previous* action's success toast — assert on fresh state (URL, reload, or unique text) instead.

## Related

[[../filament-patterns]] · [[../../frontend/design-system]] · [[ux-states]] · [[perceived-performance]]
