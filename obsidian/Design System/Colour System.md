---
tags: [flowflex, design, colours, palette]
domain: Design System
status: built
last_updated: 2026-05-06
---

# Colour System

The complete FlowFlex colour palette. **Do not use any colour not listed here.** Never create one-off hex values.

The palette concept: **deep ocean meets open sky.** Rich teal as primary — trustworthy, distinctly non-generic. Warm neutrals prevent coldness. Accent colours used sparingly and only for semantic meaning.

## Primary Palette — Ocean

The signature colours of FlowFlex. Used for primary actions, active navigation states, key data callouts, and brand moments.

| Token | Hex | Usage |
|---|---|---|
| `ocean-950` | `#061820` | Darkest — text on light, dark nav background |
| `ocean-900` | `#0D2D3F` | Dark surfaces, sidebar backgrounds |
| `ocean-800` | `#0F3D56` | Dark UI elements |
| `ocean-700` | `#135F7F` | Hover states on dark surfaces |
| `ocean-600` | `#1A7FA8` | Active navigation, link colour |
| `ocean-500` | `#2199C8` | **Primary brand teal — CTAs, active states** |
| `ocean-400` | `#4BB3DC` | Hover states on primary buttons |
| `ocean-300` | `#7FCCE9` | Light accents, icons on white |
| `ocean-200` | `#AADFF3` | Very light teal fills |
| `ocean-100` | `#D4F0FA` | Background tints, hover fills |
| `ocean-50` | `#EBF8FD` | Subtle section backgrounds |

**Primary action colour:** `ocean-500` `#2199C8`
**Primary text colour (dark mode):** `ocean-50` `#EBF8FD`

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

Warm amber/gold. Used for warnings, highlights, premium features, and secondary CTAs.

| Token | Hex | Usage |
|---|---|---|
| `tide-600` | `#B45309` | Warning text, amber badge text |
| `tide-500` | `#D97706` | Warning icons, highlight accents |
| `tide-400` | `#F59E0B` | Warning states |
| `tide-200` | `#FDE68A` | Warning backgrounds |
| `tide-100` | `#FEF3C7` | Very light warning fills |
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

## Colour Usage Rules

### Do

- Use `ocean-500` as the primary action colour exclusively
- Use semantic colours only for their intended meaning
- Use domain colours only within that domain's context
- Ensure all text meets WCAG AA contrast (4.5:1 for body, 3:1 for large text)
- Use `slate-300` for all default borders
- Use `slate-100` for page backgrounds

### Do Not

- Mix ocean and semantic colours (don't use `success-500` for a CTA)
- Use domain colours outside their domain
- Use more than 3 colours in any single component
- Create new one-off colours — always use the scale
- Use `ocean-950` as a background (too dark, reads as black)
- Use pure `#000000` or `#FFFFFF` — use the scale

## Related

- [[Brand Foundation]]
- [[Typography]]
- [[Filament Implementation]]
- [[Dark Mode]]
- [[Data Visualisation]]
