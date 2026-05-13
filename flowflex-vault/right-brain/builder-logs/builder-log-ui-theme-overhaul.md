---
type: builder-log
module: ui-theme-overhaul
domain: Design System / All Panels
panel: all
phase: cross-cutting
started: 2026-05-13
status: in-progress
color: "#F97316"
left_brain_source: "[[brand-foundation]]"
last_updated: 2026-05-13
---

# Builder Log — UI Theme Overhaul (All Filament Panels)

Cross-cutting platform design pass. Replaces default Filament grey look with FlowFlex brand identity across all 28 domain panels.

---

## Sessions

### 2026-05-13 — Dark sidebar + brand identity + panel hub integration

**Objective**: Make logged-in Filament panels look like FlowFlex, not generic Filament. Specifically: dark sidebar, branded header, and panel hub integrated into topbar instead of floating FAB.

**Files Created**

- `resources/css/filament/shared/flowflex-theme.css`
  - Dark sidebar: `#111827` background (Gray-900 — matches brand neutral-900)
  - Nav item states: default `#9CA3AF`, hover `#F3F4F6` on `#1F2937` bg, active `#F9FAFB` with domain primary icon
  - Nav group labels: `#4B5563`, 0.625rem, uppercase, 0.09em tracking
  - Topbar: white bg + `#E5E7EB` bottom border, no shadow
  - Page body: `#F9FAFB` (Gray-50)
  - Typography: Inter via `->font('Inter')` panel API
  - Table/form polish: `rounded-xl`, subtle shadow on `.fi-ta-ctn`
  - Dark mode variants for all overrides
  - `ff-panels-label` helper class for responsive "Panels" label in hub button

- `resources/views/filament/brand/logo.blade.php`
  - Indigo gradient icon (30×30px rounded square + flow mark SVG)
  - "FlowFlex" wordmark with violet "Flex" suffix
  - CSS context-aware: dark text in topbar, white text in dark sidebar

**Files Modified**

- All 28 `resources/css/filament/*/theme.css` — added `@import '../shared/flowflex-theme.css'`
- All 28 `app/Providers/Filament/*PanelProvider.php` — added:
  - `->brandLogo(fn () => view('filament.brand.logo'))`
  - `->font('Inter')`
- `app/Providers/PanelHubServiceProvider.php` — switched render hook from `BODY_END` → `TOPBAR_END`
- `resources/views/filament/shared/panel-hub.blade.php` — full redesign:
  - Was: floating FAB (position: fixed, bottom-right)
  - Now: inline topbar button with icon + "Panels" label + active count badge
  - Dropdown opens downward from topbar end, 28rem wide, 4-column panel grid
  - Groups with domain colors, current panel highlighted, inactive dimmed at 40% opacity
  - Footer with panel count and "Manage modules →" link

**Decisions Made**

- Panel hub moved to topbar (TOPBAR_END hook) — cleaner, more integrated, avoids overlap with content
- Dark sidebar `#111827` chosen over pure black for warmth; matches brand `--color-neutral-900`
- No `@apply` in shared theme CSS — uses direct property overrides since they're unlayered and beat Filament's `@layer components`

**Patterns Used**

- Unlayered CSS beats `@layer components` in CSS Cascade Layers (Tailwind v4 architecture)
- `->brandLogo(fn () => view(...))` for per-panel logo injection (Filament 5 API)
- `->font('Inter')` auto-loads Inter 400/500/600/700 from Bunny Fonts (GDPR-safe EU CDN)

**Remaining Work**

- Test visually in browser (requires `npm run dev` + app running)
- Consider adding domain-specific accent color stripe in topbar left edge per active panel
- Panel hub: hover states on panel tiles currently rely on Alpine's inline-style approach — could be improved with CSS classes once theme is confirmed
- Dark mode: verified CSS rules exist, not yet browser-tested

---

## Gaps Discovered

None.

---

## Related

- [[brand-foundation]] — design system source of truth for colors, typography, panel theming
- [[colour-system]] — domain color palette
