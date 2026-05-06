---
tags: [flowflex, design, typography, fonts]
domain: Design System
status: built
last_updated: 2026-05-06
---

# Typography

The FlowFlex type system. Use the scale — never pick arbitrary font sizes.

## Type Stack

**Primary: Inter**
Clean, legible, neutral. Designed for screen interfaces.

```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

**Monospace: JetBrains Mono**
Used in code blocks, API key displays, terminal output within the platform.

```css
font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;
```

## Type Scale

Based on a modular scale of 1.25 (Major Third).

| Token | Size | Line Height | Weight | Usage |
|---|---|---|---|---|
| `text-display` | 48px / 3rem | 1.1 | 700 | Hero headings, landing pages only |
| `text-h1` | 36px / 2.25rem | 1.2 | 700 | Page titles |
| `text-h2` | 28px / 1.75rem | 1.3 | 600 | Section headings |
| `text-h3` | 22px / 1.375rem | 1.4 | 600 | Sub-section headings, card titles |
| `text-h4` | 18px / 1.125rem | 1.4 | 600 | Component headings |
| `text-h5` | 16px / 1rem | 1.5 | 600 | Small headings, table headers |
| `text-h6` | 14px / 0.875rem | 1.5 | 600 | Micro headings, badge labels |
| `text-body-lg` | 16px / 1rem | 1.7 | 400 | Primary body text |
| `text-body` | 14px / 0.875rem | 1.6 | 400 | Secondary body, most UI text |
| `text-body-sm` | 13px / 0.8125rem | 1.5 | 400 | Helper text, captions, secondary labels |
| `text-caption` | 12px / 0.75rem | 1.5 | 400 | Timestamps, micro labels |
| `text-overline` | 11px / 0.6875rem | 1.4 | 600 | Section labels (uppercase, tracked) |
| `text-code` | 13px / 0.8125rem | 1.6 | 400 | Code snippets, monospace data |

## Font Weights in Use

| Weight | Token | Usage |
|---|---|---|
| 400 | `font-normal` | Body text, form values, table cells |
| 500 | `font-medium` | Labels, navigation items, secondary emphasis |
| 600 | `font-semibold` | Headings h3–h6, button text, active nav |
| 700 | `font-bold` | h1, h2, key metrics, strong emphasis |

Never use 800 or 900 weights. They read too heavy against the calm brand tone.

## Typography Rules

- **Always use the type scale.** Never pick an arbitrary `font-size`.
- **Never use italic** in data-heavy UI (tables, forms). Reserve for quoted text and literary emphasis.
- **Overline text** (`text-overline`) is always uppercase and letter-spaced: `text-transform: uppercase; letter-spacing: 0.08em;`
- **Heading hierarchy is strict.** Never skip levels (h1 → h3 without h2).
- **Paragraph max-width:** 680px for long-form text (knowledge base, documentation). UI text has no max-width constraint.
- **Numbers in data tables and dashboards:** use `font-variant-numeric: tabular-nums;` so digits align vertically.

## Related

- [[Brand Foundation]]
- [[Colour System]]
- [[Spacing & Layout]]
- [[Component Library]]
- [[Filament Implementation]]
