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
| `SIDEBAR_FOOTER` | collapse toggle + workspace switcher (trigger + modal, replaces the old "Your panels" chips 2026-07-05) + user card (account menu) | `filament.chrome.sidebar-footer` view (includes `workspace-switcher`) |
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

## 3. Panel switching = footer workspace switcher only · account menu = sidebar user card

Cross-panel navigation lives in the workspace switcher in the `SIDEBAR_FOOTER` (trigger above the user card opens the selection modal; `WorkspacePanels`-filtered), NOT in the profile dropdown. `userMenuItems(PanelSwitchItems::…)` was removed — two switchers is one too many. The "Your panels" chips and the `SIDEBAR_NAV_START` trigger were folded into this single footer switcher (owner request 2026-07-05); each modal row carries its domain color as `--ws-c` for tinted tiles/hover/current states, and clicking a row dims the others instantly (`ff-leaving`) while the navigation runs behind the motion.

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
8. **Overlay scrollbars hide scrollbar bugs.** Headless Chromium (and many dev setups) paints overlay scrollbars = invisible; the user's classic-Windows scrollbars paint real stubs. The recurring "weird icon top-right of every tabs bar" was the vendor tabs nav (`overflow: auto`) overflowing by exactly the skin's 1px underline border -> a 1px-tall vertical scrollbar stub. Fix: `overflow-y: hidden` + `scrollbar-width: none` + `::-webkit-scrollbar { display: none }` on `.fi-tabs`. Reproduce scrollbar-class bugs with `chromium.launch({ args: ['--disable-features=OverlayScrollbar'] })` and measure `offsetWidth - clientWidth` on the suspect box.
9. **Schemas `Tabs` vendor-centers its nav.** `.fi-sc-tabs` flex-centers a content-width `.fi-tabs` — underline tabs need `display:flex; flex-direction:column; align-items:stretch` on the wrapper + `width:100%` on the nav.
10. **Render-hook placement facts (measured):** `SIDEBAR_LOGO_AFTER` children land as direct `.fi-sidebar-header` children (siblings of `.fi-sidebar-header-logo-ctn`, which is a column flex). Multiple `->renderHook()` calls on the same slot stack in registration order. `SIDEBAR_NAV_START` renders above the nav groups (the workspace-switcher trigger lived there until 2026-07-05; it's now in the footer view). Modals launched from sidebar chrome: Alpine `x-teleport="body"` for the overlay, `x-on:keydown.escape.window` to close (workspace-switcher blade is the template).
11. **Design tokens must flip in `.dark`, not per component.** The ff-* palette was light-only for two days — every custom page shipped white-on-white in dark mode until a single `.dark { --ff-paper/--ff-card/--ff-ink/... }` block flipped the tokens globally. Corollary trap: a token that flips (like `--ff-ink`, dark text → light text) can no longer double as a *background* — pin fixed-color surfaces (the ink sidebar rail) to a non-flipping token (`--ff-flow-bg`). Native date inputs additionally need `color-scheme: light dark` or their pickers stay white.
12. **The sidebar-header toggle is absolutely positioned** (`.ff-side-toggle-wrp`) — anything that makes the header taller parks the toggle over new content and it steals pointer events. Playwright "element intercepts pointer events" on a header child = check the toggle overlay first.
12. **`@view-transition { navigation: auto }` permanently freezes the incoming page** after Livewire's `window.location` redirects — the cross-document transition never activates, rendering suspends, `requestAnimationFrame` stops firing forever (verified headless AND headed Chromium, 2026-07-05; Playwright symptom: every click times out "waiting for element to be … stable"). For panel→panel one-system feel use the `.fi-main-ctn` entrance fade (`ff-page-in`, reduced-motion gated) instead — chrome holds still, canvas crossfades through the shared paper background. Diagnostic when clicks hang: probe `requestAnimationFrame` liveness, and A/B with `page.emulateMedia({ reducedMotion: 'reduce' })`.

## Related

[[../filament-patterns]] · [[../../frontend/design-system]] · [[ux-states]] · [[perceived-performance]]
