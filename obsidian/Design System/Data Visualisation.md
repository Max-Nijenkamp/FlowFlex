---
tags: [flowflex, design, charts, data-viz]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Data Visualisation

Charts and graphs follow the same calm, trustworthy aesthetic as the rest of the platform.

Updated for 2026: Colour-blind accessible palette with alternative encodings, dark mode chart overrides, responsive chart behaviour, AI-generated insight callouts, sparkline components, and trend arrow standards.

## Chart Colour Palette

Use colours in this order for multi-series charts. Always use this sequence — never ad-hoc colour selection.

| Index | Hex | Name | Colour-blind safe |
|---|---|---|---|
| 1 | `#2199C8` | Ocean (primary) | Yes |
| 2 | `#7C3AED` | Violet | Yes |
| 3 | `#059669` | Emerald | Yes (with pattern) |
| 4 | `#D97706` | Amber | Yes |
| 5 | `#DB2777` | Pink | Yes |
| 6 | `#0284C7` | Sky | Near-duplicate with Ocean in protanopia |
| 7 | `#EA580C` | Orange | Yes |
| 8 | `#475569` | Slate | Yes |

**Never use red in charts unless it encodes danger/negative values.**

### Colour-Blind Accessible Palette

For charts where colour-blind accessibility is required (financial reports, shared dashboards), use this alternative sequence which has been tested against deuteranopia, protanopia, and tritanopia:

| Index | Hex | Name |
|---|---|---|
| 1 | `#2199C8` | Ocean |
| 2 | `#E8A838` | Marigold |
| 3 | `#6240AC` | Plum |
| 4 | `#3FA66F` | Sage |
| 5 | `#C94C4C` | Brick |
| 6 | `#4D9DE0` | Cornflower |
| 7 | `#89BF94` | Mint |
| 8 | `#8C7355` | Warm brown |

**Always supplement colour with a secondary encoding** for critical distinctions: use patterns/textures, direct labels, or different mark shapes (e.g., circle, square, triangle on scatter plots).

## Chart Style Rules

### Light Mode

```
Background:      white — no grey backgrounds behind charts
Grid lines:      slate-100 (#F3F4F6) — very subtle, horizontal only
Axis lines:      none by default — let grid lines do the work
Axis labels:     text-caption (12px) slate-400
Data labels:     text-caption (12px) slate-700, only when data density allows
Tooltip:         white card, shadow-md, radius-md, ocean-500 3px top border
Legend:          below chart, horizontal, text-body-sm (13px) slate-600
Zero line:       slate-300, 1px, when chart includes negative values
```

### Dark Mode

```
Background:      #1A1F2E (surface token)
Grid lines:      #2D3348 (border-subtle token)
Axis labels:     slate-400 (#9CA3AF)
Data labels:     slate-300 (#D1D5DB)
Tooltip:         #252C4A (level-3 surface), shadow-lg, ocean-400 top border
Legend:          slate-400 text
Zero line:       #3D4461
```

### Chart Construction Rules

- No 3D effects — ever
- No gradients on bars — solid fills only
- Area charts: 15% opacity fill under the line, full-opacity line
- No chart junk (no decorative borders, no shadows on bars, no background patterns)
- **Gridlines:** horizontal only, not vertical, except scatter plots (both)
- Axis starts at zero for bar charts — never truncate the Y-axis on a bar chart
- Line charts may start Y-axis at a non-zero value if the variation range is meaningful

### Chart Animations

No chart animations by default. Enable only on:
- Dashboard widget initial load (bars/lines draw in over 400ms `ease-decelerate`)
- Metric card reveal
- Never animate on data update — just swap the values

Always disable animations with `prefers-reduced-motion: reduce`.

## Responsive Chart Behaviour

Charts must adapt at different container widths. Use container queries (see [[Spacing & Layout]]).

| Container width | Chart behaviour |
|---|---|
| > 600px | Full chart with all axis labels and legend |
| 400px – 600px | Reduce axis label density, legend below chart |
| 300px – 400px | Hide axis labels, tooltip-only, minimal legend |
| < 300px | Sparkline only — no axes, no legend |

## Chart Types by Use Case

| Use case | Chart type | Notes |
|---|---|---|
| Revenue over time | Line chart (area variant) | Ocean primary colour, area at 15% opacity |
| Revenue by category | Horizontal bar chart | Sorted descending |
| Conversion funnel | Funnel chart | Sequential ocean shades, step-to-step % labels |
| KPI vs target | Gauge or radial progress | Ocean fill, slate-100 track |
| KPI simple trend | Metric card + sparkline | See Sparklines below |
| Team workload distribution | Stacked bar | |
| Pipeline by stage | Horizontal stacked bar | Each stage a distinct colour from the sequence |
| Geographic distribution | Choropleth map | Sequential ocean scale, not categorical colours |
| Correlation | Scatter plot | Both grid lines, circle marks, optional regression line |
| Part-to-whole (≤ 5 segments) | Donut chart | Direct labels, no legend if possible |
| Part-to-whole (> 5 segments) | Horizontal stacked bar | Donut at > 5 is unreadable |
| Distribution | Histogram | Bin boundaries clearly labelled |
| Two metrics over time | Dual-axis line chart | Use sparingly — dual axes are cognitively expensive |
| Heatmap (day×hour) | Calendar heatmap | Ocean scale for volume, white for zero |

**Never use:** Pie charts. Bubble charts with > 20 data points. Radar/spider charts.

## Sparklines

Sparklines are inline mini-charts used in metric cards and table cells to show trend at a glance.

```
Width:  100% of container (typically 120px)
Height: 32px
Type:   Line only (no area fill for sparklines)
Colour: Depends on trend direction:
        Positive trend → success-500
        Negative trend → danger-500
        Neutral/flat   → slate-400
No:     Axis, labels, legend, tooltips
```

**Trend Arrow Standards:**

| Trend | Icon | Colour | Label |
|---|---|---|---|
| Positive (% up) | `arrow-trending-up` | `success-600` | "+12.4%" |
| Negative (% down) | `arrow-trending-down` | `danger-600` | "-3.2%" |
| Flat (< 0.5% change) | `minus` | `slate-500` | "0%" |

Trend direction alone is not enough — always show the percentage value. For metrics where "down is good" (e.g., support ticket volume, churn rate), invert the colour logic and make this explicit in the label: "▼ 3.2% (improved)".

## AI-Generated Insight Callouts

When AI analyses a chart and generates a natural language insight, display it as a callout below the chart.

```
Style:     ocean-50 background, ocean-300 left border (3px), radius-md
Icon:      sparkles (ocean-400, 16px)
Text:      text-body-sm (13px), slate-700
Attribution: "AI insight · " in slate-400, followed by the insight text
Dismiss:   x-mark icon (16px, slate-400), on hover slate-600
```

Example:
> AI insight · Revenue from the Enterprise tier grew 34% month-over-month, driven by 3 new accounts. The SMB tier remained flat.

AI insights must:
- Be based on data visible in the chart — never hallucinated extrapolations
- Link to underlying data when possible ("3 new accounts →" links to the account list filtered for that period)
- Be dismissible
- Refresh when the chart's date range or filters change

## Data Tables Accompanying Charts

Every chart should offer a "View as table" toggle. The table:
- Shows the exact data used to render the chart
- Is formatted with tabular numerals, currency symbols, and date formatting consistent with the platform locale
- Is exportable (CSV/XLSX button)
- Accessible to screen readers as a proper `<table>` element

## Accessibility in Charts

- All charts must have a `title` and `desc` element in the SVG for screen reader context
- Provide a data table as an alternative (see above)
- Do not rely on colour alone — use patterns, labels, or icons as secondary encodings
- Interactive charts (hover tooltips, clickable segments): keyboard accessible via Tab + Enter
- ARIA: `role="img"` on the SVG container, `aria-label="[chart title] — [brief description]"`

## Related

- [[Colour System]]
- [[Custom Dashboards]]
- [[Report Builder]]
- [[AI & Conversational UI]]
