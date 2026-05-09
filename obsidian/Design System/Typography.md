---
tags: [flowflex, design, typography, fonts]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Typography

The FlowFlex type system. Use the scale — never pick arbitrary font sizes.

Updated for 2026: Inter variable font, fluid typography scale for marketing, optical sizing, and updated font loading strategy.

## Type Stack

### Primary: Inter (Variable)

In 2026, Inter ships as a full variable font (`Inter Variable`). Use the variable font for all new work — it eliminates weight-specific file requests and enables smooth weight transitions.

```css
/* Variable font — preferred */
@font-face {
  font-family: 'Inter';
  src: url('/fonts/InterVariable.woff2') format('woff2');
  font-weight: 100 900;
  font-style: normal;
  font-display: swap;
  font-named-instance: 'Regular';
}

/* CSS usage */
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
font-optical-sizing: auto; /* Enables optical size axis if available */
```

**Font feature settings for UI text:**
```css
font-feature-settings:
  'liga' 1,   /* Standard ligatures */
  'calt' 1,   /* Contextual alternates */
  'kern' 1;   /* Kerning */
```

**For numeric data (tables, dashboards):**
```css
font-variant-numeric: tabular-nums;
font-feature-settings: 'tnum' 1, 'lnum' 1; /* Tabular, lining figures */
```

### Monospace: JetBrains Mono

Used in code blocks, API key displays, terminal output, and JSON/formula fields.

```css
font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', ui-monospace, monospace;
font-variant-ligatures: contextual; /* Enable programming ligatures */
```

### Display: Inter (wght 700–800)

For marketing hero headings only. Use the variable font's upper weight range. In-app headings stay within the 600–700 range.

## Type Scale — App UI

Based on a modular scale of 1.25 (Major Third). These are fixed pixel values for the application UI — not fluid.

| Token | Size | Line Height | Weight | Letter Spacing | Usage |
|---|---|---|---|---|---|
| `text-display` | 48px / 3rem | 1.1 | 700 | -0.02em | Hero headings, landing pages only |
| `text-h1` | 36px / 2.25rem | 1.2 | 700 | -0.015em | Page titles |
| `text-h2` | 28px / 1.75rem | 1.3 | 600 | -0.01em | Section headings |
| `text-h3` | 22px / 1.375rem | 1.4 | 600 | -0.005em | Sub-section headings, card titles |
| `text-h4` | 18px / 1.125rem | 1.4 | 600 | 0 | Component headings |
| `text-h5` | 16px / 1rem | 1.5 | 600 | 0 | Small headings, table headers |
| `text-h6` | 14px / 0.875rem | 1.5 | 600 | 0 | Micro headings, badge labels |
| `text-body-lg` | 16px / 1rem | 1.7 | 400 | 0 | Primary body text |
| `text-body` | 14px / 0.875rem | 1.6 | 400 | 0 | Secondary body, most UI text |
| `text-body-sm` | 13px / 0.8125rem | 1.5 | 400 | 0 | Helper text, captions, secondary labels |
| `text-caption` | 12px / 0.75rem | 1.5 | 400 | 0 | Timestamps, micro labels |
| `text-overline` | 11px / 0.6875rem | 1.4 | 600 | 0.08em | Section labels (uppercase, tracked) |
| `text-code` | 13px / 0.8125rem | 1.6 | 400 | 0 | Code snippets, monospace data |

### Negative Letter Spacing on Large Headings

In 2026, standard practice for display and heading sizes is to tighten letter spacing slightly at large sizes. Inter's optical metrics benefit from this — it makes headings feel more intentional and less defaulty. The values above are baked in.

## Fluid Typography Scale — Marketing Site Only

The public marketing site uses fluid typography that scales continuously between viewport breakpoints. Use `clamp()` — never media query font-size overrides.

```css
/* Marketing site only — not used in-app */
:root {
  --font-display: clamp(2.5rem, 5vw + 1rem, 4.5rem);   /* 40px → 72px */
  --font-h1:      clamp(2rem, 3.5vw + 0.75rem, 3.5rem); /* 32px → 56px */
  --font-h2:      clamp(1.5rem, 2.5vw + 0.5rem, 2.5rem); /* 24px → 40px */
  --font-h3:      clamp(1.25rem, 1.5vw + 0.5rem, 1.875rem); /* 20px → 30px */
  --font-body:    clamp(1rem, 0.5vw + 0.875rem, 1.125rem); /* 16px → 18px */
}
```

Do **not** use `clamp()` sizing inside the application panels — fixed sizes give predictable UI layouts.

## Font Weights in Use

| Weight | Token | Variable font axis | Usage |
|---|---|---|---|
| 400 | `font-normal` | `wght: 400` | Body text, form values, table cells |
| 500 | `font-medium` | `wght: 500` | Labels, navigation items, secondary emphasis |
| 600 | `font-semibold` | `wght: 600` | Headings h3–h6, button text, active nav |
| 700 | `font-bold` | `wght: 700` | h1, h2, key metrics, strong emphasis |

Never use 800 or 900 weights in the application. Marketing display headings may use 800 via the variable axis. They read too heavy for calm, trust-building interfaces.

## CSS Custom Property Token Definitions

```css
@layer tokens {
  :root {
    --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --font-mono: 'JetBrains Mono', 'Fira Code', ui-monospace, monospace;

    --text-display: 3rem;       /* 48px */
    --text-h1:      2.25rem;    /* 36px */
    --text-h2:      1.75rem;    /* 28px */
    --text-h3:      1.375rem;   /* 22px */
    --text-h4:      1.125rem;   /* 18px */
    --text-h5:      1rem;       /* 16px */
    --text-h6:      0.875rem;   /* 14px */
    --text-body-lg: 1rem;       /* 16px */
    --text-body:    0.875rem;   /* 14px */
    --text-body-sm: 0.8125rem;  /* 13px */
    --text-caption: 0.75rem;    /* 12px */
    --text-overline: 0.6875rem; /* 11px */
    --text-code:    0.8125rem;  /* 13px */
  }
}
```

## Font Loading Strategy

```html
<!-- In <head>, before stylesheets -->
<link rel="preload" href="/fonts/InterVariable.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/fonts/JetBrainsMono.woff2" as="font" type="font/woff2" crossorigin>
```

`font-display: swap` prevents invisible text during font load. Fallback fonts (`-apple-system`, `BlinkMacSystemFont`) are selected to minimise layout shift — they have similar metrics to Inter.

## Typography Rules

- **Always use the type scale.** Never pick an arbitrary `font-size` or use `text-[15px]` Tailwind syntax.
- **Use CSS custom properties.** Reference `var(--text-body)` in component-level CSS, not hardcoded values.
- **Never use italic** in data-heavy UI (tables, forms). Reserve for quoted text and literary emphasis.
- **Overline text** (`text-overline`) is always uppercase and letter-spaced: `text-transform: uppercase; letter-spacing: 0.08em;`
- **Heading hierarchy is strict.** Never skip levels (h1 → h3 without h2).
- **Paragraph max-width:** 680px for long-form text (knowledge base, documentation). UI text has no max-width constraint.
- **Numbers in data tables and dashboards:** use `font-variant-numeric: tabular-nums;` always.
- **Negative tracking on large headings:** headings h1–h3 use the letter-spacing values in the scale above — do not override to 0.
- **AI-generated text:** use the same type scale — never visually differentiate AI text with a different font. The "AI draft" badge (tide-100 bg) indicates origin.

## Related

- [[Brand Foundation]]
- [[Colour System]]
- [[Spacing & Layout]]
- [[Component Library]]
- [[Filament Implementation]]
