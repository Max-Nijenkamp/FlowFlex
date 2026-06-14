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
| `SIDEBAR_FOOTER` | "Your panels" switcher chips + user card | `App\Support\Filament\SidebarFooter` |
| `TOPBAR_START` | panel breadcrumb (`HR & people / Employees`) | `App\Support\Filament\TopbarCrumb` |
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

## 3. Panel switching = sidebar chips only

Cross-panel navigation lives in the `SIDEBAR_FOOTER` chips (`SidebarFooter`, `canAccessPanel`-filtered), NOT in the profile dropdown. `userMenuItems(PanelSwitchItems::…)` was removed — two switchers is one too many.

## 4. Spotlight ⌘K

`App\Livewire\Spotlight` (BODY_END) — panel-scoped: nav (resources+pages, `canAccess`-filtered), quick-create, records via `$panel->getGlobalSearchProvider()`. Restore panel context with `Filament::setCurrentPanel()` (Livewire update requests don't run panel routing). Alpine gotcha: use **server-rendered stable `data-index`** for active-row tracking — `$el.querySelectorAll(...).indexOf($el)` resolves wrong in the teleported overlay and selects every row. Panels drop `globalSearchKeyBindings` (Spotlight owns mod+k). filament-patterns item 14.

## 5. Brand-aware accents

The skin never hardcodes a domain color — it rides Filament's `--primary-*` (set by each panel's `->colors()`). One skin file, every panel correct: active nav item, stat corner ticks, pagination, selection bar all use `var(--primary-500)`.

## Related

[[../filament-patterns]] · [[../../frontend/design-system]] · [[ux-states]] · [[perceived-performance]]
