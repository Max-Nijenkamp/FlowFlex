---
tags: [flowflex, design, spacing, layout, grid]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Spacing & Layout

4px base unit system. All spacing values are multiples of 4. No arbitrary values.

Updated for 2026: CSS container queries, dynamic viewport units, logical properties, and subgrid support.

## Spacing Scale

| Token | Value | CSS Custom Property | Usage |
|---|---|---|---|
| `space-1` | 4px | `--space-1` | Icon-text gap, tight inline spacing |
| `space-2` | 8px | `--space-2` | Within components (icon padding, badge padding) |
| `space-3` | 12px | `--space-3` | Between related elements within a component |
| `space-4` | 16px | `--space-4` | Standard padding inside cards, standard gap |
| `space-5` | 20px | `--space-5` | Generous padding inside larger components |
| `space-6` | 24px | `--space-6` | Section padding, between card and card |
| `space-8` | 32px | `--space-8` | Section gap in page layouts |
| `space-10` | 40px | `--space-10` | Large section gaps |
| `space-12` | 48px | `--space-12` | Page-level vertical rhythm |
| `space-16` | 64px | `--space-16` | Hero-level spacing |
| `space-20` | 80px | `--space-20` | Marketing section gaps |
| `space-24` | 96px | `--space-24` | Large marketing layout spacing |

**Rule:** All padding, margin, and gap values must be from this scale. No arbitrary values like `px-[17px]`.

## Border Radius

| Token | Value | CSS Custom Property | Usage |
|---|---|---|---|
| `radius-sm` | 4px | `--radius-sm` | Badges, tags, small chips |
| `radius-md` | 6px | `--radius-md` | Buttons, inputs, small cards |
| `radius-lg` | 8px | `--radius-lg` | Cards, panels, modals |
| `radius-xl` | 12px | `--radius-xl` | Large cards, sidebar items |
| `radius-2xl` | 16px | `--radius-2xl` | Feature cards, banners |
| `radius-full` | 9999px | `--radius-full` | Pills, avatars, toggles |

Never use `rounded-none` on interactive elements. Everything has at least `radius-sm`.

## Elevation & Shadow

FlowFlex uses minimal, purposeful shadows. Shadows communicate layering, not decoration.

| Level | CSS | Usage |
|---|---|---|
| `shadow-none` | `none` | Flat elements, table rows |
| `shadow-xs` | `0 1px 2px rgba(10, 15, 20, 0.06)` | Input fields (focused), subtle lift |
| `shadow-sm` | `0 1px 3px rgba(10, 15, 20, 0.10), 0 1px 2px rgba(10, 15, 20, 0.06)` | Cards (default) |
| `shadow-md` | `0 4px 6px rgba(10, 15, 20, 0.08), 0 2px 4px rgba(10, 15, 20, 0.06)` | Dropdown menus, popovers |
| `shadow-lg` | `0 10px 15px rgba(10, 15, 20, 0.08), 0 4px 6px rgba(10, 15, 20, 0.04)` | Modals, slide-over panels |
| `shadow-xl` | `0 20px 25px rgba(10, 15, 20, 0.10), 0 10px 10px rgba(10, 15, 20, 0.04)` | Full-screen overlays |

**Rule:** Never use coloured shadows. Shadow colour is always based on `slate-950` at low opacity.

## Page Layout Structure

All authenticated pages follow this structure:

```
┌─────────────────────────────────────────────────────┐
│  SIDEBAR (256px fixed)  │  MAIN CONTENT AREA        │
│                          │                           │
│  [Logo]                  │  [Page Header]            │
│  [Nav sections]          │    [Breadcrumb]           │
│                          │    [Title + Actions]      │
│                          │  [Content]                │
│                          │    [Filters/Search bar]   │
│                          │    [Data table / Cards]   │
│                          │                           │
└─────────────────────────────────────────────────────┘
```

- **Main content area padding:** `space-8` (32px) all sides on desktop, `space-4` (16px) on mobile
- **Max content width:** 1280px centered. Never full-bleed on very wide screens.
- **Use logical properties:** `padding-inline` / `padding-block` instead of `padding-left`/`padding-top` for i18n readiness.

## Page Header Structure

```
Row 1: Breadcrumb (slate-500, text-caption, / separator)
Row 2: Page title (text-h1) + Action buttons (right-aligned)
Row 3 (optional): Subtitle / description (text-body slate-500)
Row 4 (optional): Tab navigation or filter bar
```

Divider (1px slate-200) separates header from content.

## Dashboard Grid

12-column CSS grid:

```css
.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: var(--space-6);
}
```

Standard column spans:
- Metric cards: `grid-column: span 3` (4 across on desktop)
- Charts: `grid-column: span 6` or `span 8`
- Tables: `grid-column: span 12` (full width)
- Sidebar widgets: `grid-column: span 4`

### CSS Subgrid for Card Internals

Use `subgrid` for card grids where internal alignment matters (e.g., a row of metric cards with variable-length labels that should all share the same label row height):

```css
.metric-card-group {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-template-rows: subgrid; /* Cards share the parent row tracks */
}
```

## Container Queries

In 2026, use CSS container queries for components that need to adapt based on their container width — not the viewport. This is essential for dashboard widgets that can be placed in different column widths.

```css
/* Define a containment context on the parent */
.widget-wrapper {
  container-type: inline-size;
  container-name: widget;
}

/* Component adapts to its container, not the viewport */
@container widget (min-width: 400px) {
  .metric-card {
    flex-direction: row;
    gap: var(--space-4);
  }
}

@container widget (max-width: 399px) {
  .metric-card {
    flex-direction: column;
    gap: var(--space-2);
  }
}
```

**Rule:** Use container queries for: dashboard widgets, card grids, sidebar-aware components. Use media queries for: page-level layout changes, navigation collapse, sidebar toggle.

## Dynamic Viewport Units

Use `dvh`/`dvw` instead of `vh`/`vw` for full-height panels on mobile, where the browser chrome changes height as the user scrolls.

```css
/* Old — broken on mobile Safari when chrome collapses */
.slide-over { height: 100vh; }

/* 2026 standard — adapts to actual viewport */
.slide-over { height: 100dvh; }
.modal-backdrop { min-height: 100dvh; }
```

## Responsive Breakpoints

| Name | Width | Layout |
|---|---|---|
| `mobile` | < 640px | Single column, collapsed sidebar |
| `tablet` | 640px – 1024px | 2-column content, collapsed sidebar (icon-only) |
| `desktop` | 1024px – 1280px | Full layout, 256px sidebar |
| `wide` | > 1280px | Full layout, content max-width capped at 1280px |

### Container Query Breakpoints (Component Level)

| Name | Container Width | Behaviour |
|---|---|---|
| `cq-sm` | < 300px | Minimal — icon only, single stat |
| `cq-md` | 300px – 500px | Compact — stacked layout |
| `cq-lg` | 500px – 700px | Standard — horizontal layout |
| `cq-xl` | > 700px | Expanded — full detail visible |

## Logical Properties Reference

Use logical properties for all directional spacing to support future RTL language support:

| Physical | Logical | Notes |
|---|---|---|
| `padding-left` | `padding-inline-start` | RTL-safe |
| `padding-right` | `padding-inline-end` | RTL-safe |
| `padding-top` | `padding-block-start` | |
| `padding-bottom` | `padding-block-end` | |
| `margin-left: auto` | `margin-inline-start: auto` | Push to end |
| `text-align: left` | `text-align: start` | |
| `border-left` | `border-inline-start` | RTL-safe accent borders |

## Related

- [[Typography]]
- [[Component Library]]
- [[Dark Mode]]
