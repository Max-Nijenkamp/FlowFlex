---
tags: [flowflex, design, components, ui]
domain: Design System
status: built
last_updated: 2026-05-06
---

# Component Library

Every UI component in FlowFlex. All components trace back to these specifications.

## Buttons

### Variants

**Primary** — ocean-500 fill, white text. The single most important action on a screen.
```
bg: ocean-500 | text: white | hover: ocean-400 | active: ocean-600
border-radius: radius-md | padding: 10px 18px | font: text-body font-semibold
```

**Secondary** — white fill, ocean-500 border and text. Secondary action alongside primary.
```
bg: white | text: ocean-600 | border: 1px ocean-200 | hover: ocean-50
border-radius: radius-md | padding: 10px 18px | font: text-body font-semibold
```

**Ghost** — transparent, slate text. Tertiary actions, cancel, dismiss.
```
bg: transparent | text: slate-600 | hover bg: slate-100 | hover text: slate-900
border-radius: radius-md | padding: 10px 18px | font: text-body font-medium
```

**Danger** — coral-500 fill, white text. Destructive actions (delete, revoke).
```
bg: danger-500 | text: white | hover: danger-700 | active: danger-700
border-radius: radius-md | padding: 10px 18px | font: text-body font-semibold
```

**Link** — no background, no border. Inline with text flow.
```
bg: none | text: ocean-600 | hover: ocean-500 underline | font: inherits context
```

### Sizes

| Size | Padding | Font size | Use |
|---|---|---|---|
| `btn-xs` | 5px 10px | 12px | Compact tables, badges |
| `btn-sm` | 7px 14px | 13px | Secondary actions, toolbars |
| `btn-md` | 10px 18px | 14px | Default |
| `btn-lg` | 13px 22px | 16px | Primary CTA, hero sections |
| `btn-xl` | 16px 28px | 18px | Marketing landing pages only |

### Button States

- **Disabled:** 50% opacity, `cursor-not-allowed`, no hover effect
- **Loading:** show spinner icon, hide label text, maintain button width
- **With icon:** icon is 16px, 6px gap between icon and label

## Form Inputs

Base style (all inputs share this):

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

## Cards

Primary content container.

```
bg: white
border: 1px solid slate-200
border-radius: radius-lg (8px)
shadow: shadow-sm
padding: space-6 (24px) default
```

### Card Variants

| Variant | Description |
|---|---|
| Default | Standard. White bg, slate-200 border. |
| Elevated | Hover state for clickable cards. Shadow increases to `shadow-md`. |
| Ghost | No background, no shadow. Dashed slate-200 border. For empty states/placeholders. |
| Highlighted | Left border accent `4px solid {domain-colour}`. For important callouts. |
| Metric | Compact card for KPI numbers. slate-50 bg, no shadow. |

## Navigation — Sidebar

**Structure:**
- Fixed left sidebar, 256px wide on desktop
- Collapses to 64px icon-only on mobile / when toggled
- Background: `ocean-900` (`#0D2D3F`)
- Logo: white version, 40px height, 24px left padding
- Domain sections with `text-overline` section labels (uppercase, ocean-300, 11px)
- Nav items: 40px height, 12px left padding, 6px border-radius, 16px icons

**Nav item states:**
```
Default:   text: ocean-200 | icon: ocean-400 | bg: transparent
Hover:     text: white     | icon: ocean-300 | bg: ocean-800/40
Active:    text: white     | icon: ocean-300 | bg: ocean-700/50 | left border: 3px ocean-400
```

Section dividers: thin `ocean-800` horizontal rule between domain groups.

## Tables

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

Empty state: centered illustration + heading + subtext + optional CTA. Never blank white space.

## Badges & Status Pills

```
Size: 5px 10px padding, 11px font, radius-full, font-medium
```

| Variant | Background | Text | Use |
|---|---|---|---|
| `badge-success` | success-100 | success-700 | Active, completed, paid |
| `badge-warning` | tide-100 | tide-600 | Pending, at risk, review |
| `badge-danger` | danger-100 | danger-700 | Overdue, failed, blocked |
| `badge-info` | ocean-100 | ocean-700 | In progress, processing |
| `badge-neutral` | slate-100 | slate-600 | Draft, inactive, archived |
| `badge-domain` | {domain}-light | {domain}-dark | Module/domain indicators |

## Modals & Slide-overs

**Modal (dialog):**
- Max width: 480px (small), 640px (medium), 800px (large)
- Background: white, `shadow-xl`, `radius-xl`
- Backdrop: `rgba(10, 15, 20, 0.50)` blur optional
- Header: `text-h4` title, close X button top-right
- Footer: action buttons right-aligned (Primary + Ghost cancel)
- Padding: `space-6` (24px) all sections

**Slide-over panel:**
- Slides in from right, 480px (small) or 640px (large)
- Full height, white bg
- Close X in top-left
- Used for record detail views, quick-edit forms

## Notifications & Toasts

```
Position: top-right, stack vertically, auto-dismiss after 5s
Width: 360px
Border-radius: radius-lg
Shadow: shadow-lg
Padding: space-4 (16px)
Font: text-body-sm

Variants (left border 4px + bg white):
  success: border: success-500
  warning: border: tide-400
  danger:  border: danger-500
  info:    border: ocean-500
```

## Empty States

**Structure:**
- SVG illustration (simple, ocean colour, 120px)
- Heading (`text-h4`, `slate-800`)
- Subtext (`text-body`, `slate-500`, max 60 chars)
- Optional CTA button (Primary)

**Examples:**
- "No employees yet — Add your first team member"
- "No invoices sent — Create your first invoice"
- "No tasks here — Start a new project"

## Loading States

- **Skeleton screens** — preferred over spinners for content areas. Match content shape.
- **Spinner** — only for button loading states and small inline contexts. 20px, `ocean-500`.
- **Page-level loading:** full skeleton screen matching the page layout. Never blank white.

## Related

- [[Typography]]
- [[Colour System]]
- [[Spacing & Layout]]
- [[Motion & Animation]]
- [[Filament Implementation]]
