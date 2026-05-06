---
tags: [flowflex, design, dark-mode]
domain: Design System
status: built
last_updated: 2026-05-06
---

# Dark Mode

FlowFlex fully supports dark mode. Triggered by system preference by default, overridable via workspace settings.

## Dark Mode Colour Mapping

| Light token | Dark value | Usage |
|---|---|---|
| `slate-100` (page bg) | `#0F1117` | Page background |
| `white` (card bg) | `#1A1F2E` | Card background |
| `slate-200` (border) | `#2D3348` | Border colour |
| `slate-300` (input border) | `#3D4461` | Input borders |
| `slate-900` (primary text) | `slate-50` `#F9FAFB` | Primary text |
| `slate-700` (body text) | `slate-300` `#D1D5DB` | Body text |
| `slate-500` (muted) | `slate-400` `#9CA3AF` | Muted text |
| `ocean-500` (primary) | `ocean-400` `#4BB3DC` | Primary action (lightened for contrast) |
| `ocean-50` (tint bg) | `ocean-900/30` | Tint backgrounds |

## Dark Mode Sidebar

Dark mode sidebar uses: `#08121A` background with `ocean-700` active states.

The sidebar already uses dark colours in light mode, so dark mode deepens it further without dramatic change.

## Dark Mode Rules

- Never invert images or illustrations — provide separate dark versions or use SVG with `currentColor`
- Shadows become lighter (reduce opacity by 50%) in dark mode
- Code blocks invert naturally — dark bg, light text
- Charts: use dark axis labels, light grid lines

## Related

- [[Colour System]]
- [[Component Library]]
- [[Spacing & Layout]]
