---
tags: [flowflex, design, colours, palette]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Colour System

The complete FlowFlex colour palette. **Do not use any colour not listed here.** Never create one-off hex values.

The palette concept: **deep ocean meets open sky.** Rich teal as primary — trustworthy, distinctly non-generic. Warm neutrals prevent coldness. Accent colours used sparingly and only for semantic meaning.

Updated for 2026: APCA contrast methodology added, Display P3 wide-gamut notes, CSS custom property token architecture, and two new domain colours for AI & Automation and Community & Social.

## Primary Palette — Ocean

The signature colours of FlowFlex. Used for primary actions, active navigation states, key data callouts, and brand moments.

| Token | Hex | P3 Equivalent | Usage |
|---|---|---|---|
| `ocean-950` | `#061820` | `color(display-p3 0.024 0.094 0.125)` | Darkest — text on light, dark nav background |
| `ocean-900` | `#0D2D3F` | `color(display-p3 0.051 0.176 0.247)` | Dark surfaces, sidebar backgrounds |
| `ocean-800` | `#0F3D56` | `color(display-p3 0.059 0.239 0.337)` | Dark UI elements |
| `ocean-700` | `#135F7F` | `color(display-p3 0.075 0.373 0.498)` | Hover states on dark surfaces |
| `ocean-600` | `#1A7FA8` | `color(display-p3 0.102 0.498 0.659)` | Active navigation, link colour |
| `ocean-500` | `#2199C8` | `color(display-p3 0.129 0.600 0.784)` | **Primary brand teal — CTAs, active states** |
| `ocean-400` | `#4BB3DC` | `color(display-p3 0.294 0.702 0.863)` | Hover states on primary buttons |
| `ocean-300` | `#7FCCE9` | `color(display-p3 0.498 0.800 0.914)` | Light accents, icons on white |
| `ocean-200` | `#AADFF3` | `color(display-p3 0.667 0.875 0.953)` | Very light teal fills |
| `ocean-100` | `#D4F0FA` | `color(display-p3 0.831 0.941 0.980)` | Background tints, hover fills |
| `ocean-50` | `#EBF8FD` | `color(display-p3 0.918 0.973 0.992)` | Subtle section backgrounds |

**Primary action colour:** `ocean-500` `#2199C8`
**Primary text colour (dark mode):** `ocean-50` `#EBF8FD`

### Display P3 Usage Note

On P3-capable displays (most modern MacOS, iOS, and modern monitors), use `color(display-p3 ...)` values inside a `@media (color-gamut: p3)` block for richer, more saturated brand colours. The sRGB hex values are the safe fallback.

```css
.btn-primary {
  background-color: #2199C8; /* sRGB fallback */
}
@media (color-gamut: p3) {
  .btn-primary {
    background-color: color(display-p3 0.129 0.600 0.784);
  }
}
```

## Secondary Palette — Slate

Neutral backbone of the UI. All text, borders, surfaces, and structural elements use this scale.

| Token | Hex | Usage |
|---|---|---|
| `slate-950` | `#0A0F14` | True black — almost never used directly |
| `slate-900` | `#111827` | Primary text colour (light mode) |
| `slate-800` | `#1F2937` | Secondary headings |
| `slate-700` | `#374151` | Body text |
| `slate-600` | `#4B5563` | Secondary body text, form labels |
| `slate-500` | `#6B7280` | Placeholder text, muted labels |
| `slate-400` | `#9CA3AF` | Disabled text, tertiary info |
| `slate-300` | `#D1D5DB` | Borders (default) |
| `slate-200` | `#E5E7EB` | Dividers, subtle borders |
| `slate-100` | `#F3F4F6` | Page background, table row alternates |
| `slate-50` | `#F9FAFB` | Card backgrounds, input backgrounds |

## Accent Palette — Tide

Warm amber/gold. Used for warnings, highlights, premium features, AI-generated content badges, and secondary CTAs.

| Token | Hex | Usage |
|---|---|---|
| `tide-600` | `#B45309` | Warning text, amber badge text |
| `tide-500` | `#D97706` | Warning icons, highlight accents |
| `tide-400` | `#F59E0B` | Warning states |
| `tide-200` | `#FDE68A` | Warning backgrounds |
| `tide-100` | `#FEF3C7` | AI draft badge backgrounds, very light warning fills |
| `tide-50` | `#FFFBEB` | Warning surface tints |

## Semantic Colours

Used exclusively for their semantic meaning. Never repurpose these for decoration.

### Success — Seafoam

| Token | Hex | Usage |
|---|---|---|
| `success-700` | `#047857` | Success text |
| `success-500` | `#10B981` | Success icons, badges |
| `success-100` | `#D1FAE5` | Success background |
| `success-50` | `#ECFDF5` | Success surface tint |

### Warning — Tide (see Tide palette above)

### Danger — Coral

| Token | Hex | Usage |
|---|---|---|
| `danger-700` | `#B91C1C` | Error text |
| `danger-500` | `#EF4444` | Error icons, destructive button |
| `danger-100` | `#FEE2E2` | Error background |
| `danger-50` | `#FFF5F5` | Error surface tint |

### Info — Ocean (use `ocean-500` and `ocean-100`)

## Module Domain Colours

Each domain has a designated accent colour. Use these colours **only within that domain's context**. Never use a domain colour outside its domain.

| Domain | Colour Name | Primary Hex | Light Hex | Usage |
|---|---|---|---|---|
| Core Platform | Ocean | `#2199C8` | `#EBF8FD` | System, settings |
| HR & People | Violet | `#7C3AED` | `#EDE9FE` | People, org |
| Projects & Work | Indigo | `#4F46E5` | `#EEF2FF` | Tasks, planning |
| Finance & Accounting | Emerald | `#059669` | `#D1FAE5` | Money, numbers |
| CRM & Sales | Blue | `#2563EB` | `#DBEAFE` | Customers, deals |
| Marketing & Content | Pink | `#DB2777` | `#FCE7F3` | Content, campaigns |
| Operations & Field | Amber | `#D97706` | `#FEF3C7` | Ops, field work |
| Analytics & BI | Purple | `#9333EA` | `#F3E8FF` | Data, reports |
| IT & Security | Slate | `#475569` | `#F1F5F9` | Systems, security |
| Legal & Compliance | Red | `#DC2626` | `#FEE2E2` | Legal, risk |
| E-commerce | Teal | `#0D9488` | `#CCFBF1` | Products, orders |
| Communications | Sky | `#0284C7` | `#E0F2FE` | Chat, comms |
| Learning & Dev | Orange | `#EA580C` | `#FFEDD5` | Courses, skills |
| AI & Automation | Cyan | `#0891B2` | `#CFFAFE` | AI features, workflows |
| Community & Social | Rose | `#E11D48` | `#FFE4E6` | Community, social features |

## CSS Custom Property Token Architecture

All colours are defined as CSS custom properties on `:root`. This enables theming, dark mode, and per-panel overrides without JavaScript.

```css
/* resources/css/tokens/colours.css */
@layer tokens {
  :root {
    /* Ocean scale */
    --color-ocean-50:  #EBF8FD;
    --color-ocean-100: #D4F0FA;
    --color-ocean-200: #AADFF3;
    --color-ocean-300: #7FCCE9;
    --color-ocean-400: #4BB3DC;
    --color-ocean-500: #2199C8;
    --color-ocean-600: #1A7FA8;
    --color-ocean-700: #135F7F;
    --color-ocean-800: #0F3D56;
    --color-ocean-900: #0D2D3F;
    --color-ocean-950: #061820;

    /* Semantic aliases */
    --color-action:        var(--color-ocean-500);
    --color-action-hover:  var(--color-ocean-400);
    --color-action-active: var(--color-ocean-600);
    --color-surface:       #FFFFFF;
    --color-surface-subtle: var(--color-slate-50);
    --color-surface-muted:  var(--color-slate-100);
    --color-border:        var(--color-slate-300);
    --color-border-subtle: var(--color-slate-200);
    --color-text-primary:  var(--color-slate-900);
    --color-text-body:     var(--color-slate-700);
    --color-text-muted:    var(--color-slate-500);
    --color-text-disabled: var(--color-slate-400);
  }

  [data-theme="dark"] {
    --color-action:        var(--color-ocean-400);
    --color-surface:       #1A1F2E;
    --color-surface-subtle: #0F1117;
    --color-surface-muted:  #0F1117;
    --color-border:        #3D4461;
    --color-border-subtle: #2D3348;
    --color-text-primary:  var(--color-slate-50);
    --color-text-body:     var(--color-slate-300);
    --color-text-muted:    var(--color-slate-400);
    --color-text-disabled: var(--color-slate-600);
  }
}
```

## APCA Contrast Standards (2026)

FlowFlex targets WCAG 2.2 AA compliance as a minimum and APCA (Accessible Perceptual Contrast Algorithm) for text-heavy contexts. APCA provides more accurate contrast measurement than the WCAG 2.1 ratio method, particularly for small text and coloured backgrounds.

### APCA Lightness Contrast (Lc) Targets

| Use case | Minimum Lc | Example |
|---|---|---|
| Body text (14px regular) | Lc 75 | `slate-700` on white: Lc 83 ✓ |
| UI text (16px regular) | Lc 60 | `slate-600` on white: Lc 72 ✓ |
| Large text (24px+ bold) | Lc 45 | `ocean-700` on white: Lc 58 ✓ |
| Placeholder / muted (14px) | Lc 40 | `slate-400` on white: Lc 42 ✓ |
| Non-text UI elements | Lc 30 | Borders, icons |
| Decorative / disabled | No requirement | |

**Tool:** Use [APCA Contrast Calculator](https://www.myndex.com/APCA/) for verification. Add to design review checklist.

**WCAG 2.2 still required for:** All interactive element focus indicators (3:1 minimum against adjacent colours).

## Colour Usage Rules

### Do

- Use `ocean-500` as the primary action colour exclusively
- Use semantic colours only for their intended meaning
- Use domain colours only within that domain's context
- Verify APCA Lc values for all text, especially on coloured backgrounds
- Use CSS custom properties (`--color-*`) not raw hex values in component CSS
- Use `slate-300` for all default borders
- Use `slate-100` for page backgrounds
- Wrap `color(display-p3 ...)` values in `@media (color-gamut: p3)` blocks

### Do Not

- Mix ocean and semantic colours (don't use `success-500` for a CTA)
- Use domain colours outside their domain
- Use more than 3 colours in any single component
- Create new one-off hex values — always use the scale or extend it with a PR
- Use `ocean-950` as a background (too dark, reads as black)
- Use pure `#000000` or `#FFFFFF` — use the scale
- Rely on colour alone to convey meaning (always pair with icon or text label)

## Related

- [[Brand Foundation]]
- [[Typography]]
- [[Filament Implementation]]
- [[Dark Mode]]
- [[Data Visualisation]]
- [[AI & Conversational UI]]
