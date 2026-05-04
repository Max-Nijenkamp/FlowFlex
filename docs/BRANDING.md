# BRANDING.md — FlowFlex Design System & Brand Guidelines

> This file defines the complete visual identity, design system, and brand language of FlowFlex.
> Every UI component, colour choice, and typographic decision in the platform must trace back to this document.
> Developers, designers, and AI assistants must read this before writing a single line of CSS or Tailwind class.

---

## 1. Brand Foundation

### 1.1 Who FlowFlex Is

FlowFlex is the operating system for modern businesses. Not a startup tool, not an enterprise monolith — something in between. It is calm, capable, and quietly powerful. The product should feel like a well-designed workspace: clean surfaces, clear hierarchy, nothing screaming for attention.

The brand communicates:
- **Trust** — finance, HR, legal data lives here. The UI must feel secure and serious.
- **Clarity** — complex data presented simply. No clutter, no noise.
- **Flow** — interactions feel smooth and continuous. Nothing is jarring or abrupt.
- **Flex** — the platform adapts to the customer. The UI should feel like it was built for them.

### 1.2 Brand Personality

| Trait | What it means in the UI |
|---|---|
| Calm | Low visual noise, generous whitespace, muted tones |
| Confident | Strong typographic hierarchy, decisive colours, no wobble |
| Modern | Clean geometry, no skeuomorphism, no gradients on surfaces |
| Trustworthy | Consistent spacing, predictable patterns, nothing surprising |
| Warm | Not cold-corporate — slight warmth in the palette, human photography where used |

### 1.3 What FlowFlex Is NOT

- Not playful or startup-cute (no loud colour pops, no rounded blobs, no confetti)
- Not cold enterprise grey (not SAP, not Salesforce Classic)
- Not minimal to the point of confusion (whitespace with purpose, not emptiness)
- Not dark-by-default (light is primary; dark mode is a choice, not a statement)

---

## 2. The Name

**FlowFlex**

Two words merged, written as one. Always written exactly as: `FlowFlex`

- Capital F, capital F, no space, no hyphen, no dot
- Never: `Flowflex`, `flow flex`, `FLOWFLEX`, `flow-flex`
- In UI headings and marketing: `FlowFlex`
- In code (namespaces, config keys, env variables): `flowflex`
- In domain names and URLs: `flowflex.com`, `app.flowflex.com`

### Tagline

**"Your business, your tools — in flow."**

Secondary tagline options (context-dependent):
- "Everything your business needs. Only what you actually use."
- "One platform. Every tool. Your way."
- "Built to flex with you."

---

## 3. Logo

### 3.1 Logo Mark Concept

The FlowFlex logo mark is a stylised **double wave form** — two fluid, overlapping curves that suggest motion, continuity, and interconnection. The waves are not symmetrical; one is slightly larger, suggesting the platform scaling around the customer's needs.

The wordmark sits to the right of the mark in all default lockups.

### 3.2 Logo Versions

| Version | Usage |
|---|---|
| **Horizontal lockup** (mark + wordmark side by side) | Primary — nav headers, email signatures, marketing |
| **Stacked lockup** (mark above wordmark) | Square contexts — app icons, favicons (32px+), social avatars |
| **Mark only** | Favicon (16px), loading spinners, very small contexts |
| **Wordmark only** | When mark is already established nearby |

### 3.3 Logo Colours

| Variant | Use on |
|---|---|
| **Ocean** (primary teal gradient mark + dark wordmark) | Light backgrounds |
| **White** (all white) | Dark backgrounds, dark nav bars |
| **Mono dark** (single colour, `#0D1F2D`) | Print, single-colour contexts |
| **Mono light** (single colour, `#FFFFFF`) | Print on dark, embossing |

### 3.4 Clear Space

Minimum clear space around the logo = the height of the capital F in the wordmark on all sides. Never crowd the logo.

### 3.5 Minimum Sizes

| Version | Minimum width |
|---|---|
| Horizontal lockup | 120px |
| Stacked lockup | 64px |
| Mark only | 24px |

---

## 4. Colour System

The FlowFlex palette is built around the concept of **deep ocean meets open sky**. The primary palette is rich teal — deep, trustworthy, and distinctly non-generic. Warm neutrals prevent the UI from feeling cold. Accent colours are used sparingly and only for semantic meaning.

### 4.1 Primary Palette — Ocean

The signature colours of FlowFlex. Used for primary actions, active navigation states, key data callouts, and brand moments.

| Name | Hex | Usage |
|---|---|---|
| `ocean-950` | `#061820` | Darkest — text on light, dark nav background |
| `ocean-900` | `#0D2D3F` | Dark surfaces, sidebar backgrounds |
| `ocean-800` | `#0F3D56` | Dark UI elements |
| `ocean-700` | `#135F7F` | Hover states on dark surfaces |
| `ocean-600` | `#1A7FA8` | Active navigation, link colour |
| `ocean-500` | `#2199C8` | **Primary brand teal** — CTAs, active states |
| `ocean-400` | `#4BB3DC` | Hover states on primary buttons |
| `ocean-300` | `#7FCCE9` | Light accents, icons on white |
| `ocean-200` | `#AADFF3` | Very light teal fills |
| `ocean-100` | `#D4F0FA` | Background tints, hover fills |
| `ocean-50`  | `#EBF8FD` | Subtle section backgrounds |

**Primary action colour:** `ocean-500` `#2199C8`
**Primary text colour (dark mode):** `ocean-50` `#EBF8FD`

### 4.2 Secondary Palette — Slate

Neutral backbone of the UI. All text, borders, surfaces, and structural elements use this scale.

| Name | Hex | Usage |
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
| `slate-50`  | `#F9FAFB` | Card backgrounds, input backgrounds |

### 4.3 Accent Palette — Tide

A warm amber/gold — used for warnings, highlights, premium features, and secondary CTAs. Provides warmth against the cool ocean palette.

| Name | Hex | Usage |
|---|---|---|
| `tide-600` | `#B45309` | Warning text, amber badge text |
| `tide-500` | `#D97706` | Warning icons, highlight accents |
| `tide-400` | `#F59E0B` | Warning states |
| `tide-200` | `#FDE68A` | Warning backgrounds |
| `tide-100` | `#FEF3C7` | Very light warning fills |
| `tide-50`  | `#FFFBEB` | Warning surface tints |

### 4.4 Semantic Colours

Used exclusively for their semantic meaning. Never repurpose these for decoration.

#### Success — Seafoam
| Name | Hex | Usage |
|---|---|---|
| `success-700` | `#047857` | Success text |
| `success-500` | `#10B981` | Success icons, badges |
| `success-100` | `#D1FAE5` | Success background |
| `success-50`  | `#ECFDF5` | Success surface tint |

#### Warning — Tide (see above)

#### Danger — Coral
| Name | Hex | Usage |
|---|---|---|
| `danger-700` | `#B91C1C` | Error text |
| `danger-500` | `#EF4444` | Error icons, destructive button |
| `danger-100` | `#FEE2E2` | Error background |
| `danger-50`  | `#FFF5F5` | Error surface tint |

#### Info — Ocean (see above, `ocean-500` and `ocean-100`)

### 4.5 Module Domain Colours

Each domain in FlowFlex has a designated accent colour used for module badges, sidebar icons, and domain-specific highlights. These are distinct but all harmonise within the overall palette.

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

### 4.6 Colour Usage Rules

**Do:**
- Use `ocean-500` as the primary action colour exclusively
- Use semantic colours only for their intended meaning
- Use domain colours only within that domain's context
- Ensure all text meets WCAG AA contrast (4.5:1 for body, 3:1 for large text)
- Use `slate-300` for all default borders
- Use `slate-100` for page backgrounds

**Do not:**
- Mix ocean and semantic colours (don't use `success-500` for a CTA)
- Use domain colours outside their domain
- Use more than 3 colours in any single component
- Create new one-off colours — always use the scale
- Use `ocean-950` as a background (too dark, reads as black)
- Use pure `#000000` or `#FFFFFF` anywhere — use the scale

---

## 5. Typography

### 5.1 Type Stack

**Primary typeface: Inter**
Clean, legible, neutral. Designed for screen interfaces. Available via Google Fonts or self-hosted.

```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

**Monospace (for code, data, IDs): JetBrains Mono**
Used in code blocks, API key displays, terminal output within the platform.

```css
font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;
```

### 5.2 Type Scale

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

### 5.3 Font Weights in Use

| Weight | Token | Usage |
|---|---|---|
| 400 | `font-normal` | Body text, form values, table cells |
| 500 | `font-medium` | Labels, navigation items, secondary emphasis |
| 600 | `font-semibold` | Headings h3–h6, button text, active nav |
| 700 | `font-bold` | h1, h2, key metrics, strong emphasis |

Never use 800 or 900 weights in the UI. They read too heavy against the calm brand tone.

### 5.4 Typography Rules

- **Always use the type scale.** Never pick an arbitrary `font-size`.
- **Never use italic** in data-heavy UI (tables, forms). Reserve for quoted text and literary emphasis.
- **Overline text** (`text-overline`) is always uppercase and letter-spaced: `text-transform: uppercase; letter-spacing: 0.08em;`
- **Heading hierarchy is strict.** Never skip levels (h1 → h3 without h2).
- **Paragraph max-width:** 680px for long-form text (knowledge base, documentation). UI text has no max-width constraint.
- **Numbers in data tables and dashboards:** use `font-variant-numeric: tabular-nums;` so digits align vertically.

---

## 6. Spacing System

FlowFlex uses a **4px base unit** spacing system. All spacing values are multiples of 4.

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

**Rule:** All padding, margin, and gap values in the codebase must be a value from this scale. No arbitrary values like `px-[17px]`.

---

## 7. Border Radius

| Token | Value | Usage |
|---|---|---|
| `radius-sm` | 4px | Badges, tags, small chips |
| `radius-md` | 6px | Buttons, inputs, small cards |
| `radius-lg` | 8px | Cards, panels, modals |
| `radius-xl` | 12px | Large cards, sidebar items |
| `radius-2xl` | 16px | Feature cards, banners |
| `radius-full` | 9999px | Pills, avatars, toggles |

Never use `rounded-none` on interactive elements. Everything has at least `radius-sm`.

---

## 8. Elevation & Shadow

FlowFlex uses **minimal, purposeful shadows**. Shadows communicate layering, not decoration.

| Level | CSS | Usage |
|---|---|---|
| `shadow-none` | `none` | Flat elements, table rows |
| `shadow-xs` | `0 1px 2px rgba(10, 15, 20, 0.06)` | Input fields (focused), subtle lift |
| `shadow-sm` | `0 1px 3px rgba(10, 15, 20, 0.10), 0 1px 2px rgba(10, 15, 20, 0.06)` | Cards (default) |
| `shadow-md` | `0 4px 6px rgba(10, 15, 20, 0.08), 0 2px 4px rgba(10, 15, 20, 0.06)` | Dropdown menus, popovers |
| `shadow-lg` | `0 10px 15px rgba(10, 15, 20, 0.08), 0 4px 6px rgba(10, 15, 20, 0.04)` | Modals, slide-over panels |
| `shadow-xl` | `0 20px 25px rgba(10, 15, 20, 0.10), 0 10px 10px rgba(10, 15, 20, 0.04)` | Full-screen overlays |

**Rule:** Never use coloured shadows. Shadow colour is always based on `slate-950` at low opacity.

---

## 9. Component Library

### 9.1 Buttons

#### Variants

**Primary** — ocean-500 fill, white text. Used for the single most important action on a screen.
```
bg: ocean-500 | text: white | hover: ocean-400 | active: ocean-600
border-radius: radius-md | padding: 10px 18px | font: text-body font-semibold
```

**Secondary** — white fill, ocean-500 border and text. Used for secondary actions alongside primary.
```
bg: white | text: ocean-600 | border: 1px ocean-200 | hover: ocean-50
border-radius: radius-md | padding: 10px 18px | font: text-body font-semibold
```

**Ghost** — transparent, slate text. Used for tertiary actions, cancel, dismiss.
```
bg: transparent | text: slate-600 | hover bg: slate-100 | hover text: slate-900
border-radius: radius-md | padding: 10px 18px | font: text-body font-medium
```

**Danger** — coral-500 fill, white text. Used for destructive actions (delete, revoke).
```
bg: danger-500 | text: white | hover: danger-700 | active: danger-700
border-radius: radius-md | padding: 10px 18px | font: text-body font-semibold
```

**Link** — no background, no border. Inline with text flow.
```
bg: none | text: ocean-600 | hover: ocean-500 underline | font: inherits context
```

#### Sizes

| Size | Padding | Font size | Use |
|---|---|---|---|
| `btn-xs` | 5px 10px | 12px | Compact tables, badges |
| `btn-sm` | 7px 14px | 13px | Secondary actions, toolbars |
| `btn-md` | 10px 18px | 14px | Default |
| `btn-lg` | 13px 22px | 16px | Primary CTA, hero sections |
| `btn-xl` | 16px 28px | 18px | Marketing landing pages only |

#### States

- **Disabled:** 50% opacity, `cursor-not-allowed`, no hover effect
- **Loading:** show spinner icon, hide label text, maintain button width
- **With icon:** icon is 16px, 6px gap between icon and label

### 9.2 Form Inputs

All inputs share the same base style:

```
height: 38px (single line)
padding: 9px 12px
font: text-body (14px) font-normal slate-900
background: white
border: 1px solid slate-300
border-radius: radius-md
transition: border-color 150ms, box-shadow 150ms

focus:
  border-color: ocean-500
  box-shadow: 0 0 0 3px rgba(33, 153, 200, 0.15)
  outline: none

error:
  border-color: danger-500
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15)

disabled:
  background: slate-100
  color: slate-400
  cursor: not-allowed
```

**Textarea:** Same style, height auto, min-height 96px.
**Select:** Same style + chevron icon right-aligned, custom styled (no system default).
**Checkbox & Radio:** Custom styled, 16px × 16px, ocean-500 when checked.
**Toggle/Switch:** 36px × 20px pill, ocean-500 when on, slate-300 when off.

**Labels:** Always above the input. `text-body-sm font-medium slate-700`. Never inline placeholder-only labels.
**Helper text:** Below input, `text-caption slate-500`.
**Error message:** Below input (replaces helper), `text-caption danger-600`.

### 9.3 Cards

The primary content container. Every card has:
- `bg: white`
- `border: 1px solid slate-200`
- `border-radius: radius-lg` (8px)
- `shadow: shadow-sm`
- `padding: space-6` (24px) by default

**Card variants:**

| Variant | Description |
|---|---|
| **Default** | Standard content card. White bg, slate-200 border. |
| **Elevated** | Hover state for clickable cards. Shadow increases to `shadow-md`. |
| **Ghost** | No background, no shadow. Dashed slate-200 border. For empty states or placeholders. |
| **Highlighted** | Left border accent `4px solid {domain-colour}`. For important callouts. |
| **Metric** | Compact card for KPI numbers. slate-50 bg, no shadow. |

### 9.4 Navigation — Sidebar

The primary navigation pattern. Always present when logged in.

**Structure:**
- Fixed left sidebar, 256px wide on desktop
- Collapses to 64px icon-only mode on mobile / when toggled
- Background: `ocean-900` (`#0D2D3F`) — the dark ocean
- Logo at top: white version of FlowFlex logo, 40px height, 24px left padding
- Domain sections with section label overlines (uppercase, `ocean-300` colour, 11px)
- Nav items: 40px height, 12px left padding, 6px border-radius, 16px icons

**Nav item states:**
```
Default:   text: ocean-200 | icon: ocean-400 | bg: transparent
Hover:     text: white     | icon: ocean-300 | bg: ocean-800/40
Active:    text: white     | icon: ocean-300 | bg: ocean-700/50 | left border: 3px ocean-400
```

**Module section dividers:** thin `ocean-800` horizontal rule between domain groups.

**Active module badge:** small pill showing the domain colour on the right side of the section header.

### 9.5 Tables

Tables are used everywhere in FlowFlex. They must be consistent.

```
Header row:
  bg: slate-50 | text: slate-600 text-overline (uppercase, 11px, tracked)
  border-bottom: 2px solid slate-200 | padding: 12px 16px

Data rows:
  bg: white | hover: slate-50
  border-bottom: 1px solid slate-100
  padding: 14px 16px
  text: slate-700 text-body

Sticky header on scroll: yes, always

Row selection (checkbox):
  Selected row: bg: ocean-50 | checkbox: ocean-500

Sorted column header:
  text: ocean-600 | sort indicator icon: ocean-400
```

**Table empty state:** Centered illustration (simple line art), 24px heading, 14px subtext, optional CTA button. Never just blank white space.

### 9.6 Badges & Status Pills

Used for status labels, module tags, and categorical labels.

**Size:** 5px 10px padding, 11px font, `radius-full`, `font-medium`

**Variants based on meaning:**

| Variant | Background | Text | Use |
|---|---|---|---|
| `badge-success` | success-100 | success-700 | Active, completed, paid |
| `badge-warning` | tide-100 | tide-600 | Pending, at risk, review |
| `badge-danger` | danger-100 | danger-700 | Overdue, failed, blocked |
| `badge-info` | ocean-100 | ocean-700 | In progress, processing |
| `badge-neutral` | slate-100 | slate-600 | Draft, inactive, archived |
| `badge-domain` | {domain}-light | {domain}-dark | Module/domain indicators |

### 9.7 Modals & Slide-overs

**Modal (dialog):**
- Max width: 480px (small), 640px (medium), 800px (large)
- Background: white, `shadow-xl`, `radius-xl`
- Backdrop: `rgba(10, 15, 20, 0.50)` blur optional
- Header: `text-h4` title, close X button top-right
- Footer: action buttons right-aligned (Primary + Ghost cancel)
- Padding: `space-6` (24px) all sections

**Slide-over panel (detail view):**
- Slides in from right, 480px wide (small) or 640px (large)
- Full height, white bg
- Close X in top-left (because it slides from right)
- Used for record detail views, quick-edit forms

### 9.8 Notifications & Toasts

Toasts appear top-right, stack vertically, auto-dismiss after 5s.

```
Width: 360px
Border-radius: radius-lg
Shadow: shadow-lg
Padding: space-4 (16px)
Font: text-body-sm

Variants:
  success: left border 4px success-500 | bg white
  warning: left border 4px tide-400    | bg white
  danger:  left border 4px danger-500  | bg white
  info:    left border 4px ocean-500   | bg white
```

### 9.9 Empty States

Every list, table, and data view needs a designed empty state.

**Structure:**
- SVG illustration (simple, ocean colour, 120px)
- Heading (`text-h4`, `slate-800`)
- Subtext (`text-body`, `slate-500`, max 60 chars)
- Optional CTA button (Primary)

**Examples:**
- "No employees yet — Add your first team member →"
- "No invoices sent — Create your first invoice →"
- "No tasks here — Start a new project →"

### 9.10 Loading States

**Skeleton screens** — always preferred over spinners for content areas. Match the shape of the content being loaded (lines for text, rectangles for cards).

**Spinner** — only for button loading states and small inline contexts. 20px, `ocean-500`.

**Page-level loading:** full skeleton screen matching the page layout. Never blank white.

---

## 10. Iconography

### 10.1 Icon Library

**Primary:** Heroicons (v2) — consistent with the Filament/Tailwind ecosystem.
**Secondary:** Phosphor Icons — for richer or more expressive moments.

Always use **outline style** as default. Use **solid style** only for active/selected states.

### 10.2 Icon Sizes

| Size | Pixels | Usage |
|---|---|---|
| `icon-xs` | 12px | Inline with micro text, compact badges |
| `icon-sm` | 16px | Inline with body text, button icons |
| `icon-md` | 20px | Navigation items, form field icons |
| `icon-lg` | 24px | Section headers, card icons |
| `icon-xl` | 32px | Feature icons, empty state |
| `icon-2xl` | 48px | Hero feature moments |

### 10.3 Icon + Text Spacing

Always 6px between icon and text at default size. Scale with icon size: 4px for small, 8px for large.

### 10.4 Domain Icons

Each domain has a designated icon used consistently across the sidebar, module cards, and domain headers:

| Domain | Icon (Heroicons) |
|---|---|
| Core Platform | `cog-6-tooth` |
| HR & People | `users` |
| Projects & Work | `rectangle-stack` |
| Finance & Accounting | `banknotes` |
| CRM & Sales | `building-office-2` |
| Marketing & Content | `megaphone` |
| Operations & Field | `wrench-screwdriver` |
| Analytics & BI | `chart-bar` |
| IT & Security | `shield-check` |
| Legal & Compliance | `scale` |
| E-commerce | `shopping-bag` |
| Communications | `chat-bubble-left-right` |
| Learning & Dev | `academic-cap` |

---

## 11. Dark Mode

FlowFlex fully supports dark mode. It is triggered by the user's system preference by default, and overridable via workspace settings.

### 11.1 Dark Mode Colour Mapping

| Light | Dark | Usage |
|---|---|---|
| `slate-100` (page bg) | `#0F1117` | Page background |
| `white` (card bg) | `#1A1F2E` | Card background |
| `slate-200` (border) | `#2D3348` | Border colour |
| `slate-300` (input border) | `#3D4461` | Input borders |
| `slate-900` (primary text) | `slate-50` | Primary text |
| `slate-700` (body text) | `slate-300` | Body text |
| `slate-500` (muted) | `slate-400` | Muted text |
| `ocean-500` (primary) | `ocean-400` | Primary action (lightened for contrast) |
| `ocean-50` (tint bg) | `ocean-900/30` | Tint backgrounds |

### 11.2 Dark Mode Sidebar

Dark mode sidebar uses an even darker background: `#08121A` with `ocean-700` active states. The sidebar already uses dark colours in light mode, so dark mode deepens it further without dramatic change.

### 11.3 Dark Mode Rules

- Never invert images or illustrations — provide separate dark versions or use SVG with currentColor
- Shadows become lighter (reduce opacity by 50%) in dark mode
- Code blocks invert naturally — dark bg, light text
- Charts: use dark axis labels, light grid lines

---

## 12. Motion & Animation

FlowFlex interactions feel smooth, not sluggish. Motion should be purposeful — it guides attention, not entertains.

### 12.1 Duration Scale

| Token | Duration | Usage |
|---|---|---|
| `duration-fast` | 100ms | Micro-interactions (hover colour change, checkbox tick) |
| `duration-base` | 150ms | Default transitions (button hover, input focus) |
| `duration-slow` | 200ms | Panel expand/collapse, dropdown open |
| `duration-slower` | 300ms | Modal enter/exit, slide-over |
| `duration-page` | 400ms | Page transitions |

### 12.2 Easing Functions

| Token | Value | Usage |
|---|---|---|
| `ease-standard` | `cubic-bezier(0.4, 0, 0.2, 1)` | Most transitions |
| `ease-decelerate` | `cubic-bezier(0, 0, 0.2, 1)` | Elements entering screen |
| `ease-accelerate` | `cubic-bezier(0.4, 0, 1, 1)` | Elements leaving screen |
| `ease-spring` | `cubic-bezier(0.34, 1.56, 0.64, 1)` | Playful moments (success animations) |

### 12.3 Motion Rules

- All hover state colour changes: `duration-base` `ease-standard`
- All focus rings: `duration-fast` `ease-standard`
- Modals enter: `duration-slower` `ease-decelerate` (scale from 95% + fade in)
- Modals exit: `duration-slow` `ease-accelerate` (scale to 95% + fade out)
- Slide-overs: `duration-slower` `ease-decelerate` (slide from right)
- Toast notifications: `duration-slow` `ease-decelerate` (slide down from top)
- Page transitions: `duration-page` (fade only, no slide)
- **Respect `prefers-reduced-motion`** — disable all animation for users who request it

---

## 13. Layout Patterns

### 13.1 Page Layout

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

**Main content area padding:** `space-8` (32px) all sides on desktop, `space-4` (16px) on mobile.

**Max content width:** 1280px centered in the main area. Never full-bleed on very wide screens.

### 13.2 Page Header

Every page has a consistent header:

```
Row 1: Breadcrumb (slate-500, text-caption, / separator)
Row 2: Page title (text-h1) + Action buttons (right-aligned)
Row 3 (optional): Subtitle / description (text-body slate-500)
Row 4 (optional): Tab navigation or filter bar
```

Divider (1px slate-200) separates header from content.

### 13.3 Dashboard Layout

Module dashboards use a **12-column grid**:
- Metric cards: 3 columns each (4 across on desktop)
- Charts: 6 or 8 columns wide
- Tables: 12 columns (full width)
- Sidebar widgets: 4 columns

### 13.4 Responsive Breakpoints

| Name | Width | Layout |
|---|---|---|
| `mobile` | < 640px | Single column, collapsed sidebar |
| `tablet` | 640px – 1024px | 2-column content, collapsed sidebar (icon-only) |
| `desktop` | 1024px – 1280px | Full layout, 256px sidebar |
| `wide` | > 1280px | Full layout, content max-width capped at 1280px |

---

## 14. Data Visualisation

Charts and graphs follow the same calm, trustworthy aesthetic as the rest of the platform.

### 14.1 Chart Colour Palette

A sequential set of 8 colours for multi-series charts. Always use in this order:

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

Never use red in charts unless it encodes danger/negative values.

### 14.2 Chart Style Rules

- Background: white (no grey backgrounds behind charts)
- Grid lines: `slate-100` (very subtle)
- Axis labels: `text-caption slate-400`
- Data labels on bars: `text-caption slate-700`
- Tooltip: white card, `shadow-md`, `radius-md`, ocean-500 accent line
- Legend: below chart, horizontal, `text-body-sm slate-600`
- No 3D effects, no gradients on bars, no chart animations by default (enable on request)

### 14.3 Chart Types by Use Case

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

---

## 15. Writing Style & Voice

### 15.1 Tone

| Context | Tone |
|---|---|
| Navigation labels | Noun-first, short ("Employees", "Invoices", "Dashboard") |
| Button labels | Verb-first, imperative ("Create invoice", "Add employee", "Send") |
| Empty states | Helpful, friendly, action-oriented |
| Error messages | Specific, calm, tell the user what to do |
| Success messages | Brief confirmation, no over-celebrating |
| Confirmation dialogs | Direct, make the consequence clear |
| Tooltips | One sentence, no period at end |
| Onboarding | Warm, guiding, not patronising |

### 15.2 Microcopy Rules

- **Button labels** are always title case with a verb: "Create Invoice", "Save Changes", "Delete Employee"
- **Navigation items** are title case nouns: "Time Tracking", "Sales Pipeline", "Knowledge Base"
- **Placeholder text** gives an example, not an instruction: "e.g. Jane Smith" not "Enter name"
- **Error messages** always say what went wrong AND what to do: "Invoice total must be greater than £0 — add at least one line item."
- **Confirmation dialogs** name the item being deleted: "Delete invoice #INV-0047?" not "Are you sure?"
- **Never say** "please" in UI text — it adds length without warmth
- **Never say** "oops" — it's not serious enough for a business tool
- **Never say** "something went wrong" without a code or suggestion
- **Use "you" not "the user"** — speak directly to the person

### 15.3 Number & Date Formatting

| Type | Format | Example |
|---|---|---|
| Currency | Symbol + 2dp + thousands separator | £1,234.50 |
| Large numbers | Abbreviate at 1k+ | 12.4k, 1.2M |
| Dates (full) | Day Month Year | 14 March 2025 |
| Dates (short) | DD MMM YYYY | 14 Mar 2025 |
| Dates (compact) | DD/MM/YYYY or MM/DD/YYYY (locale-aware) | 14/03/2025 |
| Time | 24h by default, locale-aware | 14:32 |
| Relative time | For recent events | "3 minutes ago", "Yesterday" |
| Percentages | 1dp unless whole number | 12.5%, 50% |
| Duration | Abbreviate | 2h 30m |

---

## 16. Filament-Specific Implementation Notes

### 16.1 Filament Theme Config

FlowFlex extends the Filament default theme. The theme override lives at `resources/css/filament/admin/theme.css`.

Key overrides:
```css
:root {
  --primary-50:  #EBF8FD;
  --primary-100: #D4F0FA;
  --primary-200: #AADFF3;
  --primary-300: #7FCCE9;
  --primary-400: #4BB3DC;
  --primary-500: #2199C8;  /* ocean-500 — primary action */
  --primary-600: #1A7FA8;
  --primary-700: #135F7F;
  --primary-800: #0F3D56;
  --primary-900: #0D2D3F;
  --primary-950: #061820;
}
```

### 16.2 Panel Colours

Each Filament panel uses its domain colour as the primary colour override:

```php
// Example: HR Panel
FilamentColor::register([
    'primary' => [
        50  => '245, 243, 255',
        // ... violet scale
        500 => '124, 58, 237',
        // ...
    ],
]);
```

### 16.3 Filament Component Overrides

- **Tables:** custom `striped()` style using `slate-50` alternating rows
- **Forms:** all inputs use FlowFlex input style via Tailwind config
- **Navigation:** sidebar colours overridden to use `ocean-900`/`ocean-800` scale
- **Widgets:** stats overview widgets use FlowFlex metric card style
- **Notifications:** mapped to FlowFlex toast design

### 16.4 Tailwind Config Extensions

```js
// tailwind.config.js additions
module.exports = {
  theme: {
    extend: {
      colors: {
        ocean: {
          50:  '#EBF8FD',
          100: '#D4F0FA',
          // ... full scale
          950: '#061820',
        },
        tide: {
          50:  '#FFFBEB',
          // ... amber warmth scale
        },
        // success, danger, slate already match Tailwind defaults
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'Fira Code', 'monospace'],
      },
      borderRadius: {
        'sm': '4px',
        'md': '6px',
        'lg': '8px',
        'xl': '12px',
        '2xl': '16px',
      },
    },
  },
}
```

---

## 17. Asset Checklist

When producing any UI screen, ensure these are in place:

- [ ] All colours from the FlowFlex palette — no arbitrary hex values
- [ ] Font sizes from the type scale — no arbitrary `text-[15px]`
- [ ] Spacing from the spacing system — no arbitrary `p-[17px]`
- [ ] Correct border-radius for the component type
- [ ] Correct icon size for the context
- [ ] All interactive states defined (hover, focus, disabled, loading)
- [ ] Dark mode colours correct
- [ ] Empty state designed for every data list
- [ ] WCAG AA contrast verified for all text
- [ ] Correct domain colour used (not ocean-500 in the HR module)
- [ ] Module badge/icon uses the correct domain icon and colour

---

*Last updated: May 2026*
*Maintained by: Max (Founder)*
*This document is the design source of truth. All UI decisions defer to this file.*
