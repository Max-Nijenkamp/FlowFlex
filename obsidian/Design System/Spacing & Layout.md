---
tags: [flowflex, design, spacing, layout, grid]
domain: Design System
status: built
last_updated: 2026-05-06
---

# Spacing & Layout

4px base unit system. All spacing values are multiples of 4. No arbitrary values.

## Spacing Scale

| Token | Value | Usage |
|---|---|---|
| `space-1` | 4px | Icon-text gap, tight inline spacing |
| `space-2` | 8px | Within components (icon padding, badge padding) |
| `space-3` | 12px | Between related elements within a component |
| `space-4` | 16px | Standard padding inside cards, standard gap |
| `space-5` | 20px | Generous padding inside larger components |
| `space-6` | 24px | Section padding, between card and card |
| `space-8` | 32px | Section gap in page layouts |
| `space-10` | 40px | Large section gaps |
| `space-12` | 48px | Page-level vertical rhythm |
| `space-16` | 64px | Hero-level spacing |
| `space-20` | 80px | Marketing section gaps |
| `space-24` | 96px | Large marketing layout spacing |

**Rule:** All padding, margin, and gap values must be from this scale. No arbitrary values like `px-[17px]`.

## Border Radius

| Token | Value | Usage |
|---|---|---|
| `radius-sm` | 4px | Badges, tags, small chips |
| `radius-md` | 6px | Buttons, inputs, small cards |
| `radius-lg` | 8px | Cards, panels, modals |
| `radius-xl` | 12px | Large cards, sidebar items |
| `radius-2xl` | 16px | Feature cards, banners |
| `radius-full` | 9999px | Pills, avatars, toggles |

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

## Page Header Structure

```
Row 1: Breadcrumb (slate-500, text-caption, / separator)
Row 2: Page title (text-h1) + Action buttons (right-aligned)
Row 3 (optional): Subtitle / description (text-body slate-500)
Row 4 (optional): Tab navigation or filter bar
```

Divider (1px slate-200) separates header from content.

## Dashboard Grid

12-column grid:
- Metric cards: 3 columns each (4 across on desktop)
- Charts: 6 or 8 columns wide
- Tables: 12 columns (full width)
- Sidebar widgets: 4 columns

## Responsive Breakpoints

| Name | Width | Layout |
|---|---|---|
| `mobile` | < 640px | Single column, collapsed sidebar |
| `tablet` | 640px – 1024px | 2-column content, collapsed sidebar (icon-only) |
| `desktop` | 1024px – 1280px | Full layout, 256px sidebar |
| `wide` | > 1280px | Full layout, content max-width capped at 1280px |

## Related

- [[Typography]]
- [[Component Library]]
- [[Dark Mode]]
