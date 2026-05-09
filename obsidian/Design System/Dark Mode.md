---
tags: [flowflex, design, dark-mode]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Dark Mode

FlowFlex fully supports dark mode. Triggered by system preference by default, overridable via workspace settings.

Updated for 2026: CSS `color-scheme` property, semantic token architecture for dark mode, surface elevation system for dark surfaces, APCA contrast verification for dark mode text, and user preference persistence.

## Dark Mode Architecture

Dark mode is implemented via CSS custom property overrides on the `[data-theme="dark"]` attribute on `<html>`. This allows:
- Server-side initial render without flash (set `data-theme` from user session before page paint)
- Instant switching without page reload
- Per-panel theme overrides if needed

```css
/* All semantic colour tokens redefine to dark values */
[data-theme="dark"] {
  color-scheme: dark; /* Tells browser to use dark scrollbars, inputs, etc. */

  --color-surface:        #1A1F2E;
  --color-surface-subtle: #0F1117;
  --color-surface-muted:  #0A0D14;
  --color-border:         #3D4461;
  --color-border-subtle:  #2D3348;
  --color-text-primary:   #F9FAFB;
  --color-text-body:      #D1D5DB;
  --color-text-muted:     #9CA3AF;
  --color-text-disabled:  #6B7280;
  --color-action:         #4BB3DC; /* ocean-400, lightened for contrast */
  --color-action-hover:   #7FCCE9; /* ocean-300 */
}
```

## Dark Mode Colour Mapping

Full reference of light → dark token values:

| Semantic token | Light value | Dark value | Notes |
|---|---|---|---|
| `--color-surface` | `#FFFFFF` | `#1A1F2E` | Card, panel backgrounds |
| `--color-surface-subtle` | `#F9FAFB` (slate-50) | `#0F1117` | Page background |
| `--color-surface-muted` | `#F3F4F6` (slate-100) | `#0A0D14` | Recessed sections |
| `--color-border` | `#D1D5DB` (slate-300) | `#3D4461` | Input borders |
| `--color-border-subtle` | `#E5E7EB` (slate-200) | `#2D3348` | Dividers, card borders |
| `--color-text-primary` | `#111827` (slate-900) | `#F9FAFB` (slate-50) | Headings, primary text |
| `--color-text-body` | `#374151` (slate-700) | `#D1D5DB` (slate-300) | Body text |
| `--color-text-muted` | `#6B7280` (slate-500) | `#9CA3AF` (slate-400) | Captions, placeholders |
| `--color-text-disabled` | `#9CA3AF` (slate-400) | `#6B7280` (slate-500) | Disabled states |
| `--color-action` | `#2199C8` (ocean-500) | `#4BB3DC` (ocean-400) | CTAs, links (lightened) |
| `--color-action-hover` | `#4BB3DC` (ocean-400) | `#7FCCE9` (ocean-300) | |
| `success-500` | `#10B981` | `#34D399` | Lightened for dark bg |
| `danger-500` | `#EF4444` | `#F87171` | Lightened for dark bg |
| `tide-500` | `#D97706` | `#FBB020` | Lightened for dark bg |

## Dark Mode Surface Elevation

In dark mode, use subtle surface lightness increases (not shadows) to convey elevation. This is how Google Material You and Apple platforms handle dark elevation — shadows are less effective on dark surfaces.

| Elevation level | Dark surface colour | Usage |
|---|---|---|
| Level 0 — Page background | `#0F1117` | App background |
| Level 1 — Cards, panels | `#1A1F2E` | Default card surface |
| Level 2 — Nested cards | `#1F2642` | Cards within cards |
| Level 3 — Dropdowns, tooltips | `#252C4A` | Popovers, dropdown menus |
| Level 4 — Modals | `#2B3252` | Modal backgrounds |

**Rule:** In dark mode, elevation is conveyed by lightening the surface colour slightly, not adding colour. Never add coloured tints to dark elevation — only value (lightness) changes.

## Dark Mode Sidebar

The sidebar in dark mode uses: `#08121A` background (deeper than the base dark surface). This creates a clear visual distinction between the sidebar and the main content area — both are dark, but the sidebar is noticeably darker.

| State | Background | Text | Icon |
|---|---|---|---|
| Default item | `#08121A` | `ocean-200` `#AADFF3` | `ocean-400` |
| Hover item | `ocean-900` `#0D2D3F` | `white` | `ocean-300` |
| Active item | `ocean-800` / 50% | `white` | `ocean-300` + left border `ocean-400` |

## APCA Contrast in Dark Mode

All text in dark mode must meet APCA Lc targets. Dark mode has different contrast dynamics than light mode — verify separately.

| Text type | Dark mode Lc minimum | Example pair | Actual Lc |
|---|---|---|---|
| Body text (14px) | Lc 70 | `#D1D5DB` on `#1A1F2E` | Lc 74 ✓ |
| Heading (24px bold) | Lc 50 | `#F9FAFB` on `#1A1F2E` | Lc 88 ✓ |
| Muted text (13px) | Lc 45 | `#9CA3AF` on `#1A1F2E` | Lc 48 ✓ |
| Ocean link text (14px) | Lc 55 | `#4BB3DC` on `#1A1F2E` | Lc 56 ✓ |
| Placeholder (14px) | Lc 35 | `#6B7280` on `#1A1F2E` | Lc 37 ✓ |

## Dark Mode Images & Illustrations

- **Never invert images** — photographs look wrong inverted and illustrations lose intent
- **SVG illustrations:** Use `currentColor` for strokes and fills where possible, so they adapt automatically
- **Raster images:** For images that look harsh on dark backgrounds, apply a subtle brightness reduction:
  ```css
  [data-theme="dark"] img:not([data-no-dark]) {
    filter: brightness(0.9) contrast(1.05);
  }
  ```
- **Logos of third parties** (integration logos, payment method icons): provide both light and dark variants in an `img` set
- **Charts and data visualisations:** see [[Data Visualisation]] — dark mode chart overrides are specified there

## Dark Mode Shadows

Shadows in dark mode should be reduced in intensity — they are less effective and can look muddy.

```css
[data-theme="dark"] {
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.30), 0 1px 2px rgba(0, 0, 0, 0.20);
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.25), 0 2px 4px rgba(0, 0, 0, 0.15);
  --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.25), 0 4px 6px rgba(0, 0, 0, 0.12);
}
```

Elevated surfaces in dark mode rely on the surface lightness system (see above) more than shadows.

## User Preference Persistence

1. On first visit: read `prefers-color-scheme` media query, set `data-theme` accordingly
2. User explicitly toggles: save preference to user profile (database) AND `localStorage` fallback
3. On subsequent pages: server sets `data-theme` from session/profile before HTML is streamed — prevents flash of wrong theme
4. Livewire navigation: preserve theme without re-reading from server on each page

```php
// Middleware: SetThemeFromUserPreference.php
// Sets data-theme on the root layout before first paint
```

## Dark Mode Rules Checklist

When building any component, verify in dark mode:

- [ ] All text meets APCA Lc targets on dark surfaces
- [ ] Focus rings are visible (`ocean-400` or `white` ring, 2px)
- [ ] Form inputs show correct `--color-border` value
- [ ] Domain colour accents remain distinguishable (test with the 8 chart colours)
- [ ] Skeleton loaders use dark shimmer variant
- [ ] SVG illustrations use `currentColor` — no hardcoded `fill="#xxx"` values
- [ ] Images have dark mode `brightness()` applied if needed
- [ ] No component uses `bg-white` directly — use `bg-[--color-surface]`
- [ ] Sidebar is correctly deep (`#08121A`) vs content area (`#0F1117`)
- [ ] Shadows are reduced-opacity dark mode variants

## Related

- [[Colour System]]
- [[Component Library]]
- [[Spacing & Layout]]
- [[Filament Implementation]]
