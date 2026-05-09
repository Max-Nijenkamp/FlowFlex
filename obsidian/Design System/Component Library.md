---
tags: [flowflex, design, components, ui]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Component Library

Every UI component in FlowFlex. All components trace back to these specifications.

Updated for 2026: command palette, AI chat widget, skeleton loaders (updated spec), virtual scrolling, drag-and-drop, rich text editor, data grid, optimistic UI patterns.

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

## Command Palette

The command palette is the power-user interface to the entire platform. Triggered by `Cmd+K` / `Ctrl+K` from anywhere in the app.

```
Overlay: centred, 640px wide, max-height 480px
Background: white (dark: #1A1F2E), shadow-xl, radius-xl
Backdrop: rgba(10,15,20,0.50) blur(4px)
Input: 48px height, text-h4, no border, full-width
Divider: 1px slate-100 between input and results
Results list: max 8 visible, scrollable
```

**Result item structure:**
- Icon (16px, domain colour or category colour)
- Label (text-body, slate-900)
- Description (text-body-sm, slate-500)
- Keyboard shortcut hint (right-aligned, text-caption, slate-400, monospace)
- Selected state: ocean-50 bg, ocean-600 left border 2px

**Sections within results:**
- Recent (last 5 visited pages)
- Actions (Create Invoice, Add Employee, etc.)
- Navigation (direct page links)
- AI Actions ("Summarise this week's activity", "Draft a report")

**AI Actions in Command Palette:**
- Prefixed with a subtle `sparkle` icon in ocean-400
- Labelled as "Ask AI: [action]"
- Enter key triggers inline AI response — not a new page

**Keyboard navigation:**
- `↑` / `↓` — navigate results
- `Enter` — execute selected result
- `Escape` — close
- Type `/` to filter to actions only
- Type `>` to filter to AI actions only

## AI Chat Widget

The persistent AI assistant available across the entire platform. Accessed via the sidebar AI button or `Cmd+Shift+A`.

See [[AI & Conversational UI]] for full specification.

**Summary:**
- Slide-in panel from right, 400px wide, full height
- Does not replace the main content area
- Context-aware — knows which page/record the user is on
- Streaming text response with typing cursor animation
- Citation bubbles for data-backed answers (links to source records)

## Skeleton Loaders

Skeleton screens are the standard loading state for all content areas. They communicate shape and structure before data arrives.

### Skeleton Principles

- Match the exact layout of the content being loaded — not a generic grey block
- Use animated gradient shimmer to indicate loading activity
- Duration before showing skeleton: 0ms for cold loads, 300ms for perceived-instant cached loads (avoids skeleton flash on fast connections)
- Replace immediately when data arrives — no fade, instant swap

### Skeleton Animation

```css
@keyframes skeleton-shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.skeleton {
  background: linear-gradient(
    90deg,
    var(--color-slate-100) 25%,
    var(--color-slate-50) 50%,
    var(--color-slate-100) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s ease-in-out infinite;
  border-radius: var(--radius-sm);
}

/* Dark mode */
[data-theme="dark"] .skeleton {
  background: linear-gradient(
    90deg,
    #2D3348 25%,
    #3D4461 50%,
    #2D3348 75%
  );
  background-size: 200% 100%;
}
```

### Skeleton Variants

**Table skeleton:**
- Header row: full-width bar, 16px height
- Data rows: alternating columns with text-width bars and a shorter badge-width bar

**Card skeleton:**
- Title bar: 60% width, 16px height
- Body lines: 3 lines at 100%, 80%, 90% width, 12px height, 8px gap

**Dashboard metric skeleton:**
- Label bar: 40% width, 10px
- Value bar: 60% width, 28px, weight hint via height
- Trend bar: 30% width, 12px

**Never use:** A spinning circle as a full-page loader. Use skeleton screens always.

## Optimistic UI Patterns

Optimistic UI means the interface updates immediately when the user takes an action, before the server confirms success. On failure, the action is rolled back.

### When to Use Optimistic UI

- Toggle switches (activate/deactivate)
- Task status changes (todo → in-progress → done)
- Like/react actions
- Reorder / drag-and-drop
- Mark as read / unread

### When NOT to Use Optimistic UI

- Financial transactions (invoices, payments)
- Delete actions (wait for server confirmation)
- Permission changes (RBAC — confirm before applying)
- Any action with external side effects (sending emails, webhooks)

### Implementation Pattern

```
1. User takes action
2. UI updates immediately (optimistic state)
3. API call made in background
4. On success: confirm (no visible change — already updated)
5. On failure: revert to previous state + show error toast
   "Could not save — your change has been undone."
```

**Visual treatment during optimistic state:**
- No change to the UI element itself
- If loading takes > 500ms, show a subtle spinner overlay on the element (not disabling it)
- Failed state: element returns to previous value + danger border flash (200ms) + toast

## Virtual Scrolling

For lists with > 200 items, use virtual scrolling. Render only visible rows plus a buffer.

**Contexts that require virtual scrolling:**
- Large contact lists
- Transaction ledgers (finance)
- Audit log / activity streams
- Employee directories at scale

**Implementation notes:**
- Row height must be fixed or measurable (dynamic heights require more complex windowing)
- Maintain scroll position across tab changes (cache scroll offset in component state)
- Keyboard navigation must work through virtualized rows (arrow keys, page up/down)
- Screen reader support: `aria-setsize` and `aria-posinset` on each row, `aria-rowcount` on the list

**Row height standards:**
- Table rows: 48px (matches standard table row padding)
- Card list items: 72px (compact) or 96px (with avatar)
- Timeline/activity items: 64px

## Drag and Drop

Used in: task boards (kanban), column reordering, dashboard widget arrangement, list reordering.

### Visual States

**Dragging item:**
- 95% scale, shadow-lg, 80% opacity
- Cursor: `grabbing`
- Use a "ghost" clone that follows the cursor — the original placeholder stays visible at reduced opacity

**Drop target (valid):**
- 2px dashed ocean-400 border
- ocean-50 background tint
- Smooth height animation to accommodate incoming item

**Drop target (invalid):**
- 2px dashed danger-300 border
- Cursor: `not-allowed`

**After drop:**
- Spring physics entry animation on the dropped item (scale 0.95 → 1.0, 200ms)
- Position updates are optimistic — see Optimistic UI section

### Accessibility for Drag and Drop

Every drag-and-drop interaction must have a keyboard-accessible equivalent:
- "Move up" / "Move down" buttons or keyboard shortcuts
- Context menu "Move to position N"
- Screen reader announces: "[Item] moved from position [X] to position [Y]"

## Rich Text Editor

Used in: Knowledge Base, Document Management, Email Marketing, Course Builder, Proposals, Company Announcements.

**Library:** ProseMirror-based (TipTap recommended for Laravel/Livewire ecosystem). Not a `contenteditable` DIV.

### Editor Toolbar (Standard)

```
[B] [I] [U] [~~] | [H1][H2][H3] | [ul][ol] | ["] | [link][img][table] | [code] | [AI]
```

- Toolbar is floating (appears above selected text) on mobile, fixed on desktop
- `[AI]` button: triggers AI assistant for selected text ("Improve writing", "Summarise", "Translate")

### Typography in Editor

- Body text: `text-body-lg` (16px) — larger than UI default for readability in document contexts
- Headings use the standard FlowFlex heading scale
- Monospace code blocks: `text-code` in ocean-900 bg code fence

### AI Integration in Editor

When text is selected and AI button clicked:
1. Contextual AI menu appears (floating, above selection)
2. Options: "Improve writing" / "Make shorter" / "Make longer" / "Fix grammar" / "Translate to..."
3. AI response shown as a diff preview (strikethrough old, underline new)
4. User accepts all, accepts some, or dismisses
5. Accepted text replaces selection with a brief tide-100 highlight flash (500ms)

## Data Grid

For finance tables, large data sets, and export-ready views. Distinct from standard tables — supports sorting, filtering, column pinning, and row grouping.

**Differences from standard table:**
- Column resizing (drag handle on header right edge)
- Column reordering (drag header to reorder)
- Column pinning (left or right, up to 3 columns)
- Row grouping with expand/collapse
- Multi-column sort (click header, Shift+click to add secondary sort)
- Inline cell editing (double-click to edit)
- Export button in toolbar (CSV, XLSX)

**Data grid height:**
- Always fixed height with internal scroll — never expands the page
- Default: 600px on desktop
- Resize handle at bottom border

**Cell types:**
- Text (left-aligned)
- Number / currency (right-aligned, tabular-nums)
- Date (locale-aware)
- Badge/pill (use standard badge component)
- Boolean (checkmark icon or dash)
- Actions (icon buttons, right-aligned)

## Related

- [[Typography]]
- [[Colour System]]
- [[Spacing & Layout]]
- [[Motion & Animation]]
- [[Filament Implementation]]
- [[AI & Conversational UI]]
