---
tags: [flowflex, design, charts, data-viz]
domain: Design System
status: built
last_updated: 2026-05-06
---

# Data Visualisation

Charts and graphs follow the same calm, trustworthy aesthetic as the rest of the platform.

## Chart Colour Palette

Use colours in this order for multi-series charts. Always use this sequence — never ad-hoc colour selection.

| Index | Hex | Name |
|---|---|---|
| 1 | `#2199C8` | Ocean (primary) |
| 2 | `#7C3AED` | Violet |
| 3 | `#059669` | Emerald |
| 4 | `#D97706` | Amber |
| 5 | `#DB2777` | Pink |
| 6 | `#0284C7` | Sky |
| 7 | `#EA580C` | Orange |
| 8 | `#475569` | Slate |

**Never use red in charts unless it encodes danger/negative values.**

## Chart Style Rules

- Background: white (no grey backgrounds behind charts)
- Grid lines: `slate-100` (very subtle)
- Axis labels: `text-caption slate-400`
- Data labels on bars: `text-caption slate-700`
- Tooltip: white card, `shadow-md`, `radius-md`, ocean-500 accent line
- Legend: below chart, horizontal, `text-body-sm slate-600`
- No 3D effects
- No gradients on bars
- No chart animations by default (enable on explicit request)

## Chart Types by Use Case

| Use case | Chart type |
|---|---|
| Revenue over time | Line chart (area variant) |
| Revenue by category | Horizontal bar chart |
| Conversion funnel | Funnel chart |
| KPI vs target | Gauge or progress bar |
| Team workload distribution | Stacked bar |
| Pipeline by stage | Horizontal stacked bar |
| Geographic distribution | Choropleth map |
| Correlation | Scatter plot |
| Part-to-whole (max 5 segments) | Donut chart |
| Part-to-whole (more segments) | Horizontal stacked bar instead |

**Never use pie charts** — they are hard to read accurately. Use donut (max 5 segments) or horizontal bar instead.

## Related

- [[Colour System]]
- [[Custom Dashboards]]
- [[Report Builder]]
